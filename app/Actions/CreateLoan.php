<?php

namespace App\Actions;

use App\Enums\BookStatus;
use App\Enums\LoanStatus;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Support\Facades\DB;

class CreateLoan
{
    public function __construct(
        protected LoanService $loanService
    ) {}

    public function handle(Member $member, array $bookCopyIds, ?User $processedBy = null, array $data = []): Loan
    {
        return DB::transaction(function () use ($member, $bookCopyIds, $processedBy, $data) {
            $bookCopyIds = array_values(array_unique(array_filter($bookCopyIds)));
            $loanDate = $data['loan_date'] ?? now()->toDateString();
            $dueDate = $data['due_date'] ?? $this->loanService->calculateDueDate($loanDate)->toDateString();

            $this->loanService->validateLoan($member, $bookCopyIds);

            $loan = Loan::create([
                'loan_number' => $data['loan_number'] ?? Loan::generateLoanNumber(),
                'member_id' => $member->id,
                'processed_by' => $processedBy?->id,
                'loan_date' => $loanDate,
                'due_date' => $dueDate,
                'status' => LoanStatus::Active,
                'extension_count' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($bookCopyIds as $bookCopyId) {
                $loan->items()->create([
                    'book_copy_id' => $bookCopyId,
                    'due_date' => $dueDate,
                    'status' => 'borrowed',
                ]);

                BookCopy::whereKey($bookCopyId)->update([
                    'status' => BookStatus::Borrowed->value,
                ]);
            }

            return $loan->load(['member', 'processedBy', 'items.bookCopy.book']);
        });
    }
}
