<?php

namespace Tests\Unit;

use App\Actions\CreateLoan;
use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected Category $category;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loanService = app(LoanService::class);
        $this->category = Category::factory()->create();
        $this->book = Book::factory()->create(['category_id' => $this->category->id]);
    }

    public function test_validate_loan_allows_active_member_and_available_copy(): void
    {
        $member = Member::factory()->create();
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        $this->loanService->validateLoan($member, [$copy->id]);

        $this->assertTrue(true);
    }

    public function test_validate_loan_rejects_blocked_member(): void
    {
        $member = Member::factory()->blocked()->create();
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Anggota tidak memenuhi syarat');

        $this->loanService->validateLoan($member, [$copy->id]);
    }

    public function test_validate_loan_rejects_unavailable_copy(): void
    {
        $member = Member::factory()->create();
        $copy = BookCopy::factory()->borrowed()->create(['book_id' => $this->book->id]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('tidak tersedia');

        $this->loanService->validateLoan($member, [$copy->id]);
    }

    public function test_create_loan_generates_number_due_date_items_and_marks_copies_borrowed(): void
    {
        $member = Member::factory()->create();
        $librarian = User::factory()->librarian()->create();
        $copies = BookCopy::factory()->count(2)->available()->create(['book_id' => $this->book->id]);

        $loan = app(CreateLoan::class)->handle($member, $copies->pluck('id')->all(), $librarian, [
            'loan_date' => '2026-05-23',
            'notes' => 'Pinjam untuk kelas.',
        ]);

        $this->assertNotEmpty($loan->loan_number);
        $this->assertEquals('2026-05-30', $loan->due_date->toDateString());
        $this->assertEquals($librarian->id, $loan->processed_by);
        $this->assertCount(2, $loan->items);

        foreach ($copies as $copy) {
            $this->assertDatabaseHas('book_copies', [
                'id' => $copy->id,
                'status' => BookStatus::Borrowed->value,
            ]);
        }
    }

    public function test_validate_loan_respects_max_books_setting(): void
    {
        app('settings')->set('loan.max_books', 1);

        $member = Member::factory()->create();
        $copies = BookCopy::factory()->count(2)->available()->create(['book_id' => $this->book->id]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('batas maksimal 1 buku');

        $this->loanService->validateLoan($member, $copies->pluck('id')->all());
    }
}
