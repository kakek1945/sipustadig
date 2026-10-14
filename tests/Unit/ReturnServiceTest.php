<?php

namespace Tests\Unit;

use App\Actions\CreateLoan;
use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\User;
use App\Services\ReturnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReturnService $returnService;
    protected Category $category;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->returnService = app(ReturnService::class);
        $this->category = Category::factory()->create();
        $this->book = Book::factory()->create(['category_id' => $this->category->id]);
    }

    public function test_process_return_updates_copy_and_creates_fine_for_overdue_item(): void
    {
        app('settings')->set('fine.daily_rate', 1000);

        $member = Member::factory()->create();
        $librarian = User::factory()->librarian()->create();
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        $loan = app(CreateLoan::class)->handle($member, [$copy->id], $librarian, [
            'loan_date' => '2026-05-01',
            'due_date' => '2026-05-07',
        ]);

        $loanItem = $loan->items()->first();
        $result = $this->returnService->processReturn($loanItem, $librarian, [
            'return_date' => '2026-05-10',
        ]);

        $this->assertSame(3, $result['late_days']);
        $this->assertSame(3000.0, $result['fine_amount']);
        $this->assertDatabaseHas('book_copies', [
            'id' => $copy->id,
            'status' => BookStatus::Available->value,
        ]);
        $this->assertDatabaseHas('fines', [
            'loan_id' => $loan->id,
            'loan_item_id' => $loanItem->id,
            'late_days' => 3,
        ]);
    }

    public function test_find_borrowed_item_by_code_returns_item(): void
    {
        $member = Member::factory()->create();
        $librarian = User::factory()->librarian()->create();
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        app(CreateLoan::class)->handle($member, [$copy->id], $librarian, ['loan_date' => '2026-05-23']);

        $item = $this->returnService->findBorrowedItemByCode($copy->copy_code);

        $this->assertNotNull($item);
        $this->assertSame($copy->id, $item->book_copy_id);
    }
}
