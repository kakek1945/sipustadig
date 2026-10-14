<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Enums\LoanStatus;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class LoanService
{
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = Loan::query()
            ->with(['member', 'processedBy', 'items.bookCopy.book'])
            ->withCount('items');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search) {
                $query->where('loan_number', 'like', "%{$search}%")
                    ->orWhereHas('member', function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('member_number', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%")
                            ->orWhere('employee_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.bookCopy', function (Builder $query) use ($search) {
                        $query->where('copy_code', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.bookCopy.book', function (Builder $query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('author', 'like', "%{$search}%");
                    });
            });
        }

        $tab = $filters['tab'] ?? 'active';
        match ($tab) {
            'active' => $query->whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value]),
            'returned' => $query->where('status', LoanStatus::Returned->value),
            'overdue' => $query->where(function (Builder $query) {
                $query->where('status', LoanStatus::Overdue->value)
                    ->orWhere(function (Builder $query) {
                        $query->whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value])
                            ->whereDate('due_date', '<', now()->toDateString());
                    });
            }),
            default => null,
        };

        if (!empty($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }

        $allowedSorts = ['loan_number', 'loan_date', 'due_date', 'created_at'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSorts, true) ? $filters['sort_by'] : 'created_at';
        $sortDir = ($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function validateLoan(Member $member, array $bookCopyIds): void
    {
        $bookCopyIds = array_values(array_unique(array_filter($bookCopyIds)));

        if ($bookCopyIds === []) {
            throw new \InvalidArgumentException('Minimal satu eksemplar buku harus dipilih.');
        }

        if (!$member->canBorrow()) {
            throw new \InvalidArgumentException('Anggota tidak memenuhi syarat untuk meminjam buku.');
        }

        $maxBooks = (int) app('settings')->get('loan.max_books', 3);
        $activeItems = $member->loans()
            ->whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value, LoanStatus::Overdue->value])
            ->withCount(['items as borrowed_items_count' => fn (Builder $query) => $query->where('status', 'borrowed')])
            ->get()
            ->sum('borrowed_items_count');

        if (($activeItems + count($bookCopyIds)) > $maxBooks) {
            throw new \InvalidArgumentException("Anggota melebihi batas maksimal {$maxBooks} buku aktif.");
        }

        $copies = BookCopy::with('book')->whereIn('id', $bookCopyIds)->get();

        if ($copies->count() !== count($bookCopyIds)) {
            throw new \InvalidArgumentException('Sebagian eksemplar buku tidak ditemukan.');
        }

        foreach ($copies as $copy) {
            if (!$copy->canBorrow()) {
                $title = $copy->book?->title ?? $copy->copy_code;
                throw new \InvalidArgumentException("Eksemplar {$title} tidak tersedia untuk dipinjam.");
            }
        }
    }

    public function calculateDueDate(?string $loanDate = null): \Carbon\Carbon
    {
        $days = (int) app('settings')->get('loan.max_borrow_days', 7);

        return \Carbon\Carbon::parse($loanDate ?? now())->addDays($days);
    }

    public function stats(): array
    {
        return [
            'active' => Loan::whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value])->count(),
            'overdue' => Loan::where(function (Builder $query) {
                $query->where('status', LoanStatus::Overdue->value)
                    ->orWhere(function (Builder $query) {
                        $query->whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value])
                            ->whereDate('due_date', '<', now()->toDateString());
                    });
            })->count(),
            'returned' => Loan::where('status', LoanStatus::Returned->value)->count(),
            'available_copies' => BookCopy::where('status', BookStatus::Available->value)->count(),
        ];
    }
}
