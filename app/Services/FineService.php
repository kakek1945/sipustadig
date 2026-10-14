<?php

namespace App\Services;

use App\Enums\FineStatus;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FineService
{
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = Fine::query()->with(['member', 'loan', 'loanItem.bookCopy.book', 'processedBy']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search) {
                $query->where('fine_number', 'like', "%{$search}%")
                    ->orWhereHas('member', function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('member_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('loan', fn (Builder $query) => $query->where('loan_number', 'like', "%{$search}%"))
                    ->orWhereHas('loanItem.bookCopy.book', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $allowedSorts = ['fine_number', 'amount', 'late_days', 'created_at', 'paid_date'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSorts, true) ? $filters['sort_by'] : 'created_at';
        $sortDir = ($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function createOverdueFine(
        Member $member,
        Loan $loan,
        LoanItem $loanItem,
        int $lateDays,
        float $amount,
        ?User $processedBy = null,
        ?string $returnDate = null
    ): ?Fine {
        if ($lateDays <= 0 || $amount <= 0) {
            return null;
        }

        $existingFine = Fine::where('loan_item_id', $loanItem->id)->first();

        if ($existingFine) {
            return $existingFine;
        }

        return Fine::create([
            'member_id' => $member->id,
            'loan_id' => $loan->id,
            'loan_item_id' => $loanItem->id,
            'processed_by' => $processedBy?->id,
            'fine_number' => Fine::generateFineNumber(),
            'late_days' => $lateDays,
            'amount' => $amount,
            'paid_amount' => 0,
            'status' => FineStatus::Pending,
            'due_date' => \Carbon\Carbon::parse($returnDate ?? now())->addDays(30)->toDateString(),
            'notes' => "Denda keterlambatan {$lateDays} hari.",
        ]);
    }

    public function pay(Fine $fine, ?float $amount = null, ?User $processedBy = null, ?string $notes = null): Fine
    {
        if ($fine->status === FineStatus::Waived) {
            throw new \InvalidArgumentException('Denda yang sudah dibebaskan tidak dapat dibayar.');
        }

        if ($fine->isPaid()) {
            throw new \InvalidArgumentException('Denda sudah lunas.');
        }

        return DB::transaction(function () use ($fine, $amount, $processedBy, $notes) {
            $payment = $amount ?? $fine->remainingAmount();

            if ($payment <= 0) {
                throw new \InvalidArgumentException('Nominal pembayaran harus lebih dari 0.');
            }

            $paidAmount = min((float) $fine->amount, (float) $fine->paid_amount + $payment);
            $isPaid = $paidAmount >= (float) $fine->amount;

            $fine->update([
                'processed_by' => $processedBy?->id ?? $fine->processed_by,
                'paid_amount' => $paidAmount,
                'status' => $isPaid ? FineStatus::Paid : FineStatus::Pending,
                'paid_date' => $isPaid ? now()->toDateString() : $fine->paid_date,
                'notes' => $notes ? trim(($fine->notes ? $fine->notes."\n" : '').$notes) : $fine->notes,
            ]);

            return $fine->fresh(['member', 'loan', 'loanItem.bookCopy.book', 'processedBy']);
        });
    }

    public function waive(Fine $fine, string $reason, ?User $processedBy = null): Fine
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('Alasan pembebasan denda wajib diisi.');
        }

        if ($fine->isPaid()) {
            throw new \InvalidArgumentException('Denda yang sudah lunas tidak dapat dibebaskan.');
        }

        return DB::transaction(function () use ($fine, $reason, $processedBy) {
            $fine->update([
                'processed_by' => $processedBy?->id ?? $fine->processed_by,
                'status' => FineStatus::Waived,
                'waiver_reason' => $reason,
                'paid_date' => now()->toDateString(),
            ]);

            return $fine->fresh(['member', 'loan', 'loanItem.bookCopy.book', 'processedBy']);
        });
    }

    public function stats(): array
    {
        return [
            'pending' => Fine::where('status', FineStatus::Pending->value)->count(),
            'paid' => Fine::where('status', FineStatus::Paid->value)->count(),
            'waived' => Fine::where('status', FineStatus::Waived->value)->count(),
            'pending_amount' => (float) Fine::where('status', FineStatus::Pending->value)
                ->selectRaw('COALESCE(SUM(amount - paid_amount), 0) as total')
                ->value('total'),
        ];
    }
}
