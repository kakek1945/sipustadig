<?php

namespace Tests\Unit;

use App\Actions\CreateLoan;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\User;
use App\Services\FineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FineService $fineService;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fineService = app(FineService::class);
        $this->book = Book::factory()->create(['category_id' => Category::factory()->create()->id]);
    }

    public function test_pay_marks_fine_paid(): void
    {
        $fine = Fine::factory()->create();
        $user = User::factory()->librarian()->create();

        $paid = $this->fineService->pay($fine, null, $user);

        $this->assertTrue($paid->isPaid());
        $this->assertDatabaseHas('fines', [
            'id' => $fine->id,
            'status' => 'paid',
        ]);
    }

    public function test_waive_requires_reason(): void
    {
        $fine = Fine::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->fineService->waive($fine, '', null);
    }

    public function test_create_overdue_fine_creates_record_once(): void
    {
        $member = Member::factory()->create();
        $librarian = User::factory()->librarian()->create();
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);
        $loan = app(CreateLoan::class)->handle($member, [$copy->id], $librarian, ['loan_date' => '2026-05-01']);
        $item = $loan->items()->first();

        $first = $this->fineService->createOverdueFine($member, $loan, $item, 4, 4000, $librarian);
        $second = $this->fineService->createOverdueFine($member, $loan, $item, 4, 4000, $librarian);

        $this->assertNotNull($first);
        $this->assertSame($first->id, $second->id);
    }
}
