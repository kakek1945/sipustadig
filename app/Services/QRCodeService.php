<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Member;

class QRCodeService
{
    public function memberPayload(Member $member): array
    {
        return [
            'app' => 'sirkulib',
            'type' => 'member',
            'member_number' => $member->member_number,
        ];
    }

    public function bookCopyPayload(BookCopy $copy): array
    {
        return [
            'app' => 'sirkulib',
            'type' => 'book_copy',
            'copy_code' => $copy->copy_code,
            'barcode' => $copy->barcode,
        ];
    }

    public function loanPayload(Loan $loan): array
    {
        return [
            'app' => 'sirkulib',
            'type' => 'loan',
            'loan_number' => $loan->loan_number,
        ];
    }

    public function returnPayload(LoanItem $loanItem): array
    {
        $loanItem->loadMissing('bookCopy');

        return [
            'app' => 'sirkulib',
            'type' => 'return',
            'copy_code' => $loanItem->bookCopy?->copy_code,
            'barcode' => $loanItem->bookCopy?->barcode,
        ];
    }

    public function inventoryPayload(BookCopy $copy): array
    {
        return [
            'app' => 'sirkulib',
            'type' => 'inventory',
            'copy_code' => $copy->copy_code,
            'barcode' => $copy->barcode,
        ];
    }

    public function encode(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function resolve(string $scan): array
    {
        $payload = $this->parse($scan);

        return match ($payload['type'] ?? null) {
            'member' => $this->resolveMember($payload['member_number'] ?? $payload['value'] ?? ''),
            'book_copy', 'copy', 'return', 'inventory' => $this->resolveBookCopy($payload['copy_code'] ?? $payload['barcode'] ?? $payload['value'] ?? ''),
            'isbn', 'book' => $this->resolveIsbn($payload['isbn'] ?? $payload['value'] ?? ''),
            'loan' => $this->resolveLoan($payload['loan_number'] ?? $payload['value'] ?? ''),
            default => $this->resolveRaw($scan),
        };
    }

    public function parse(string $scan): array
    {
        $scan = trim($scan);
        $json = json_decode($scan, true);

        if (is_array($json)) {
            return $json;
        }

        if (str_starts_with(strtoupper($scan), 'SIRKULIB:')) {
            $parts = explode(':', $scan, 3);

            if (count($parts) === 3) {
                return [
                    'type' => strtolower($parts[1]) === 'copy' ? 'book_copy' : strtolower($parts[1]),
                    'value' => $parts[2],
                ];
            }
        }

        return ['type' => null, 'value' => $scan];
    }

    protected function resolveRaw(string $scan): array
    {
        $scan = trim($scan);

        $member = Member::where('member_number', $scan)->first();
        if ($member) {
            return $this->memberResult($member);
        }

        $copy = BookCopy::with('book.category')
            ->where('copy_code', $scan)
            ->orWhere('barcode', $scan)
            ->first();
        if ($copy) {
            return $this->bookCopyResult($copy);
        }

        if (preg_match('/^(97[89])?\d{9}[\dX]$/i', $scan)) {
            return $this->resolveIsbn($scan);
        }

        return $this->notFoundResult($scan);
    }

    protected function resolveMember(string $memberNumber): array
    {
        $member = Member::where('member_number', trim($memberNumber))->first();

        return $member ? $this->memberResult($member) : $this->notFoundResult($memberNumber, 'member');
    }

    protected function resolveBookCopy(string $code): array
    {
        $copy = BookCopy::with('book.category')
            ->where('copy_code', trim($code))
            ->orWhere('barcode', trim($code))
            ->first();

        return $copy ? $this->bookCopyResult($copy) : $this->notFoundResult($code, 'book_copy');
    }

    protected function resolveIsbn(string $isbn): array
    {
        $book = Book::with('category')
            ->where('isbn', trim($isbn))
            ->first();

        if (!$book) {
            return $this->notFoundResult($isbn, 'isbn');
        }

        return [
            'found' => true,
            'type' => 'isbn',
            'input' => $isbn,
            'book' => [
                'id' => $book->id,
                'isbn' => $book->isbn,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'publish_year' => $book->publish_year,
                'edition' => $book->edition,
                'category' => $book->category?->name,
                'rack_location' => $book->rack_location,
            ],
        ];
    }

    protected function resolveLoan(string $loanNumber): array
    {
        $loan = Loan::with('member')
            ->where('loan_number', trim($loanNumber))
            ->first();

        if (!$loan) {
            return $this->notFoundResult($loanNumber, 'loan');
        }

        return [
            'found' => true,
            'type' => 'loan',
            'loan' => [
                'id' => $loan->id,
                'loan_number' => $loan->loan_number,
                'status' => $loan->status?->value ?? $loan->status,
                'loan_date' => $loan->loan_date?->toDateString(),
                'due_date' => $loan->due_date?->toDateString(),
                'member' => [
                    'id' => $loan->member?->id,
                    'member_number' => $loan->member?->member_number,
                    'name' => $loan->member?->name,
                ],
            ],
        ];
    }

    protected function memberResult(Member $member): array
    {
        return [
            'found' => true,
            'type' => 'member',
            'member' => [
                'id' => $member->id,
                'member_number' => $member->member_number,
                'name' => $member->name,
                'email' => $member->email,
                'type' => $member->type?->value ?? $member->type,
                'class' => $member->class,
                'year_level' => $member->year_level,
                'status' => $member->status,
                'can_borrow' => $member->canBorrow(),
            ],
        ];
    }

    protected function bookCopyResult(BookCopy $copy): array
    {
        return [
            'found' => true,
            'type' => 'book_copy',
            'book_copy' => [
                'id' => $copy->id,
                'copy_code' => $copy->copy_code,
                'barcode' => $copy->barcode,
                'status' => $copy->status?->value ?? $copy->status,
                'condition' => $copy->condition,
                'book' => [
                    'id' => $copy->book?->id,
                    'isbn' => $copy->book?->isbn,
                    'title' => $copy->book?->title,
                    'author' => $copy->book?->author,
                    'category' => $copy->book?->category?->name,
                    'rack_location' => $copy->book?->rack_location,
                ],
            ],
        ];
    }

    protected function notFoundResult(string $input, ?string $type = null): array
    {
        return [
            'found' => false,
            'type' => $type,
            'input' => trim($input),
            'message' => 'Hasil scan tidak ditemukan.',
        ];
    }
}
