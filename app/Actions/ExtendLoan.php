<?php

namespace App\Actions;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExtendLoan
{
    public function handle(Loan $loan, ?User $processedBy = null, ?int $days = null, ?string $notes = null): Loan
    {
        return DB::transaction(function () use ($loan, $processedBy, $days, $notes) {
            $loan = $loan->fresh(['items']);

            if (!$loan->canExtend()) {
                throw new \InvalidArgumentException('Peminjaman ini tidak dapat diperpanjang.');
            }

            if ($loan->isOverdue()) {
                throw new \InvalidArgumentException('Peminjaman terlambat harus dikembalikan terlebih dahulu.');
            }

            $extendDays = $days ?? (int) app('settings')->get('loan.max_borrow_days', 7);
            $newDueDate = $loan->due_date->copy()->addDays($extendDays)->toDateString();

            $loan->update([
                'processed_by' => $processedBy?->id ?? $loan->processed_by,
                'due_date' => $newDueDate,
                'status' => LoanStatus::Extended,
                'extension_count' => $loan->extension_count + 1,
                'notes' => $notes ? trim(($loan->notes ? $loan->notes."\n" : '').$notes) : $loan->notes,
            ]);

            $loan->items()
                ->where('status', 'borrowed')
                ->update(['due_date' => $newDueDate]);

            return $loan->fresh(['member', 'processedBy', 'items.bookCopy.book']);
        });
    }
}
