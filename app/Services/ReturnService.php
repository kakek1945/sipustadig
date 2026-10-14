<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Enums\LoanStatus;
use App\Models\LoanItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    public function __construct(
        protected FineService $fineService
    ) {}

    public function getBorrowedItems(array $filters = []): LengthAwarePaginator
    {
        $query = LoanItem::query()
            ->with(['loan.member', 'bookCopy.book'])
            ->where('status', 'borrowed')
            ->whereHas('loan', function (Builder $query) {
                $query->whereIn('status', [
                    LoanStatus::Active->value,
                    LoanStatus::Extended->value,
                    LoanStatus::Overdue->value,
                ]);
            });

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search) {
                $query->whereHas('loan', fn (Builder $query) => $query->where('loan_number', 'like', "%{$search}%"))
                    ->orWhereHas('loan.member', function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('member_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('bookCopy', function (Builder $query) use ($search) {
                        $query->where('copy_code', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%");
                    })
                    ->orWhereHas('bookCopy.book', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
            });
        }

        return $query
            ->orderBy('due_date')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findBorrowedItemByCode(string $code): ?LoanItem
    {
        return LoanItem::query()
            ->with(['loan.member', 'bookCopy.book'])
            ->where('status', 'borrowed')
            ->whereHas('bookCopy', function (Builder $query) use ($code) {
                $query->where('copy_code', $code)
                    ->orWhere('barcode', $code);
            })
            ->first();
    }

    public function calculateLateDays(LoanItem $loanItem, ?string $returnDate = null): int
    {
        $returnedAt = \Carbon\Carbon::parse($returnDate ?? now())->startOfDay();
        $dueDate = $loanItem->due_date->copy()->startOfDay();

        if ($returnedAt->lessThanOrEqualTo($dueDate)) {
            return 0;
        }

        return (int) $dueDate->diffInDays($returnedAt);
    }

    public function calculateFineAmount(int $lateDays): float
    {
        if ($lateDays <= 0) {
            return 0;
        }

        $dailyRate = (float) app('settings')->get('fine.daily_rate', 1000);
        $maxAmount = (float) app('settings')->get('fine.max_amount', 50000);

        return min($lateDays * $dailyRate, $maxAmount);
    }

    public function processReturn(LoanItem $loanItem, ?User $processedBy = null, array $data = []): array
    {
        return DB::transaction(function () use ($loanItem, $processedBy, $data) {
            $loanItem = $loanItem->fresh(['loan.member', 'bookCopy.book']);

            if ($loanItem->status !== 'borrowed') {
                throw new \InvalidArgumentException('Eksemplar ini sudah diproses pengembaliannya.');
            }

            $returnDate = $data['return_date'] ?? now()->toDateString();
            $lateDays = $this->calculateLateDays($loanItem, $returnDate);
            $fineAmount = $this->calculateFineAmount($lateDays);

            $loanItem->update([
                'return_date' => $returnDate,
                'status' => 'returned',
                'notes' => $data['notes'] ?? $loanItem->notes,
            ]);

            $loanItem->bookCopy->update([
                'status' => BookStatus::Available->value,
            ]);

            $loan = $loanItem->loan;
            $fine = $this->fineService->createOverdueFine(
                $loan->member,
                $loan,
                $loanItem,
                $lateDays,
                $fineAmount,
                $processedBy,
                $returnDate
            );

            if (!$loan->items()->where('status', 'borrowed')->exists()) {
                $loan->update([
                    'status' => LoanStatus::Returned,
                    'return_date' => $returnDate,
                ]);
            } elseif ($loan->due_date->isPast()) {
                $loan->update(['status' => LoanStatus::Overdue]);
            }

            return [
                'loan' => $loan->fresh(['member', 'items.bookCopy.book']),
                'loan_item' => $loanItem->fresh(['bookCopy.book']),
                'fine' => $fine,
                'late_days' => $lateDays,
                'fine_amount' => $fineAmount,
            ];
        });
    }

    public function stats(): array
    {
        return [
            'borrowed' => LoanItem::where('status', 'borrowed')->count(),
            'overdue' => LoanItem::where('status', 'borrowed')
                ->whereDate('due_date', '<', now()->toDateString())
                ->count(),
            'returned_today' => LoanItem::where('status', 'returned')
                ->whereDate('return_date', now()->toDateString())
                ->count(),
        ];
    }
}
