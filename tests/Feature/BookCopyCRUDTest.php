<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCopyCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected User $librarian;
    protected User $student;
    protected Category $category;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->librarian = User::factory()->librarian()->create();
        $this->student = User::factory()->create(); // defaults to Student role
        $this->category = Category::factory()->create();
        $this->book = Book::factory()->create(['category_id' => $this->category->id]);
    }

    public function test_librarian_can_access_copies_index(): void
    {
        BookCopy::factory()->count(3)->create(['book_id' => $this->book->id]);

        $response = $this->actingAs($this->librarian)
            ->get(route('book-copies.index'));

        $response->assertStatus(200);
        $response->assertViewHas('copies');
    }

    public function test_student_can_also_access_copies_index(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('book-copies.index'));

        $response->assertStatus(200);
    }

    public function test_librarian_can_create_single_copy(): void
    {
        $copyData = [
            'book_id' => $this->book->id,
            'copy_code' => 'TEST-COPY-CODE-1',
            'barcode' => 'TEST-BARCODE-1',
            'qr_code' => 'TEST-QRCODE-1',
            'status' => BookStatus::Available->value,
            'condition' => 'Baik',
            'acquisition_date' => '2026-05-23',
            'purchase_price' => 45000,
        ];

        $response = $this->actingAs($this->librarian)
            ->post(route('book-copies.store'), $copyData);

        $copy = BookCopy::where('copy_code', 'TEST-COPY-CODE-1')->first();
        $this->assertNotNull($copy);

        $response->assertRedirect(route('book-copies.show', $copy->id));
        $this->assertDatabaseHas('book_copies', [
            'copy_code' => 'TEST-COPY-CODE-1',
            'purchase_price' => 45000
        ]);
    }

    public function test_librarian_can_bulk_generate_copies(): void
    {
        $bulkData = [
            'book_id' => $this->book->id,
            'bulk_count' => 5,
            'condition' => 'Sangat Baik',
            'acquisition_date' => '2026-05-23',
            'purchase_price' => 60000,
        ];

        $response = $this->actingAs($this->librarian)
            ->post(route('book-copies.store'), $bulkData);

        $response->assertRedirect(route('books.show', $this->book->id));
        $this->assertEquals(5, BookCopy::where('book_id', $this->book->id)->count());
        $this->assertDatabaseHas('book_copies', [
            'book_id' => $this->book->id,
            'condition' => 'Sangat Baik'
        ]);
    }

    public function test_student_cannot_create_copy(): void
    {
        $copyData = [
            'book_id' => $this->book->id,
            'copy_code' => 'HACK-CODE',
            'status' => BookStatus::Available->value,
            'condition' => 'Baik',
        ];

        $response = $this->actingAs($this->student)
            ->post(route('book-copies.store'), $copyData);

        $response->assertStatus(403);
    }

    public function test_librarian_can_view_copy_detail(): void
    {
        $copy = BookCopy::factory()->create(['book_id' => $this->book->id]);

        $response = $this->actingAs($this->librarian)
            ->get(route('book-copies.show', $copy->id));

        $response->assertStatus(200);
        $response->assertViewHas('copy');
    }

    public function test_librarian_can_update_copy(): void
    {
        $copy = BookCopy::factory()->create(['book_id' => $this->book->id, 'condition' => 'Baik']);

        $response = $this->actingAs($this->librarian)
            ->put(route('book-copies.update', $copy->id), [
                'book_id' => $this->book->id,
                'condition' => 'Rusak Ringan',
                'status' => BookStatus::Damaged->value,
            ]);

        $response->assertRedirect(route('book-copies.show', $copy->id));
        $this->assertDatabaseHas('book_copies', [
            'id' => $copy->id,
            'condition' => 'Rusak Ringan',
            'status' => BookStatus::Damaged->value,
        ]);
    }

    public function test_student_cannot_update_copy(): void
    {
        $copy = BookCopy::factory()->create(['book_id' => $this->book->id]);

        $response = $this->actingAs($this->student)
            ->put(route('book-copies.update', $copy->id), [
                'book_id' => $this->book->id,
                'condition' => 'Hacked',
                'status' => BookStatus::Available->value,
            ]);

        $response->assertStatus(403);
    }

    public function test_librarian_can_delete_copy(): void
    {
        $copy = BookCopy::factory()->create(['book_id' => $this->book->id, 'status' => BookStatus::Available]);

        $response = $this->actingAs($this->librarian)
            ->delete(route('book-copies.destroy', $copy->id));

        $response->assertRedirect(route('books.show', $this->book->id));
        $this->assertDatabaseMissing('book_copies', ['id' => $copy->id]);
    }

    public function test_student_cannot_delete_copy(): void
    {
        $copy = BookCopy::factory()->create(['book_id' => $this->book->id, 'status' => BookStatus::Available]);

        $response = $this->actingAs($this->student)
            ->delete(route('book-copies.destroy', $copy->id));

        $response->assertStatus(403);
    }

    public function test_librarian_can_access_print_label_page(): void
    {
        $copy = BookCopy::factory()->create(['book_id' => $this->book->id]);

        $response = $this->actingAs($this->librarian)
            ->get(route('book-copies.print-label', $copy->id));

        $response->assertStatus(200);
        $response->assertViewHas('copy');
        $response->assertViewHas('qrPayload');
    }
}
