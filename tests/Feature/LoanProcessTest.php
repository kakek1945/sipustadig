<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanProcessTest extends TestCase
{
    use RefreshDatabase;

    protected User $librarian;
    protected User $student;
    protected Member $member;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->librarian = User::factory()->librarian()->create();
        $this->student = User::factory()->create();
        $category = Category::factory()->create();
        $this->book = Book::factory()->create(['category_id' => $category->id]);
        $this->member = Member::factory()->create();
    }

    public function test_librarian_can_access_loans_index_and_create_page(): void
    {
        BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        $this->actingAs($this->librarian)
            ->get(route('loans.index'))
            ->assertStatus(200)
            ->assertViewHas('loans');

        $this->actingAs($this->librarian)
            ->get(route('loans.create'))
            ->assertStatus(200)
            ->assertViewHasAll(['members', 'bookCopies']);
    }

    public function test_librarian_can_create_loan(): void
    {
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        $response = $this->actingAs($this->librarian)
            ->post(route('loans.store'), [
                'member_id' => $this->member->id,
                'book_copy_ids' => [$copy->id],
                'loan_date' => '2026-05-23',
            ]);

        $loan = Loan::where('member_id', $this->member->id)->first();
        $this->assertNotNull($loan);

        $response->assertRedirect(route('loans.show', $loan->id));
        $this->assertDatabaseHas('loan_items', [
            'loan_id' => $loan->id,
            'book_copy_id' => $copy->id,
            'status' => 'borrowed',
        ]);
        $this->assertDatabaseHas('book_copies', [
            'id' => $copy->id,
            'status' => BookStatus::Borrowed->value,
        ]);
    }

    public function test_librarian_can_view_loan_detail(): void
    {
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        $loan = app(\App\Actions\CreateLoan::class)->handle(
            $this->member,
            [$copy->id],
            $this->librarian,
            ['loan_date' => '2026-05-23']
        );

        $this->actingAs($this->librarian)
            ->get(route('loans.show', $loan->id))
            ->assertStatus(200)
            ->assertViewHas('loan');
    }

    public function test_student_cannot_create_loan(): void
    {
        $copy = BookCopy::factory()->available()->create(['book_id' => $this->book->id]);

        $this->actingAs($this->student)
            ->post(route('loans.store'), [
                'member_id' => $this->member->id,
                'book_copy_ids' => [$copy->id],
            ])
            ->assertStatus(403);
    }

    public function test_store_validates_required_member_and_copy(): void
    {
        $this->actingAs($this->librarian)
            ->post(route('loans.store'), [])
            ->assertSessionHasErrors(['member_id', 'book_copy_ids']);
    }
}
