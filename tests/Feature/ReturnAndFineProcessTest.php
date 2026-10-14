<?php

namespace Tests\Feature;

use App\Actions\CreateLoan;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnAndFineProcessTest extends TestCase
{
    use RefreshDatabase;

    protected User $librarian;
    protected Member $member;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->librarian = User::factory()->librarian()->create();
        $this->member = Member::factory()->create();
        $this->book = Book::factory()->create(['category_id' => Category::factory()->create()->id]);
    }

    public function test_librarian_can_access_return_and_fine_pages(): void
    {
        $this->actingAs($this->librarian)
            ->get(route('returns.index'))
            ->assertStatus(200)
            ->assertViewHas('borrowedItems');

        $this->actingAs($this->librarian)
            ->get(route('fines.index'))
            ->assertStatus(200)
            ->assertViewHas('fines');
    }

    public function test_return_lookup_returns_json(): void
    {
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);
        app(CreateLoan::class)->handle($this->member, [$copy->id], $this->librarian, ['loan_date' => '2026-05-01']);

        $this->actingAs($this->librarian)
            ->getJson(route('returns.lookup', ['book_code' => $copy->copy_code]))
            ->assertStatus(200)
            ->assertJsonFragment(['code' => $copy->copy_code]);
    }

    public function test_librarian_can_pay_and_waive_fines(): void
    {
        $fine = Fine::factory()->create();

        $this->actingAs($this->librarian)
            ->post(route('fines.pay', $fine->id), ['amount' => $fine->amount])
            ->assertRedirect(route('fines.show', $fine->id));

        $this->assertDatabaseHas('fines', [
            'id' => $fine->id,
            'status' => 'paid',
        ]);

        $anotherFine = Fine::factory()->create();

        $this->actingAs($this->librarian)
            ->post(route('fines.waive', $anotherFine->id), ['reason' => 'Kebijakan sekolah'])
            ->assertRedirect(route('fines.show', $anotherFine->id));

        $this->assertDatabaseHas('fines', [
            'id' => $anotherFine->id,
            'status' => 'waived',
        ]);
    }

    public function test_librarian_can_process_return_through_scan_flow(): void
    {
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);
        app(CreateLoan::class)->handle($this->member, [$copy->id], $this->librarian, ['loan_date' => '2026-05-01', 'due_date' => '2026-05-07']);

        $this->actingAs($this->librarian)
            ->post(route('returns.process'), [
                'book_code' => $copy->copy_code,
                'return_date' => '2026-05-10',
            ])
            ->assertRedirect(route('returns.index'));
    }
}
