<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Enums\BookStatus;
use App\Services\BookCopyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCopyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BookCopyService $bookCopyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookCopyService = new BookCopyService();
    }

    public function test_get_paginated_returns_paginated_copies(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        BookCopy::factory()->count(10)->create(['book_id' => $book->id]);

        $result = $this->bookCopyService->getPaginated(['per_page' => 5]);

        $this->assertCount(5, $result->items());
        $this->assertEquals(10, $result->total());
    }

    public function test_get_paginated_filters_by_search_query(): void
    {
        $category = Category::factory()->create();
        $bookA = Book::factory()->create(['title' => 'Laravel Book', 'category_id' => $category->id]);
        $bookB = Book::factory()->create(['title' => 'Vue Book', 'category_id' => $category->id]);

        BookCopy::factory()->create([
            'book_id' => $bookA->id,
            'copy_code' => 'LAR-001',
            'barcode' => 'BAR-LAR-1'
        ]);
        BookCopy::factory()->create([
            'book_id' => $bookB->id,
            'copy_code' => 'VUE-001',
            'barcode' => 'BAR-VUE-1'
        ]);

        // Search by book title
        $searchBook = $this->bookCopyService->getPaginated(['search' => 'Laravel']);
        $this->assertCount(1, $searchBook->items());
        $this->assertEquals('LAR-001', $searchBook->items()[0]->copy_code);

        // Search by copy code
        $searchCode = $this->bookCopyService->getPaginated(['search' => 'VUE-001']);
        $this->assertCount(1, $searchCode->items());
        $this->assertEquals('VUE-001', $searchCode->items()[0]->copy_code);
    }

    public function test_get_paginated_filters_by_status_and_condition(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        BookCopy::factory()->create([
            'book_id' => $book->id,
            'status' => BookStatus::Damaged,
            'condition' => 'Rusak Ringan'
        ]);
        BookCopy::factory()->create([
            'book_id' => $book->id,
            'status' => BookStatus::Available,
            'condition' => 'Baik'
        ]);

        $resultStatus = $this->bookCopyService->getPaginated(['status' => BookStatus::Damaged->value]);
        $this->assertCount(1, $resultStatus->items());
        $this->assertEquals(BookStatus::Damaged, $resultStatus->items()[0]->status);

        $resultCondition = $this->bookCopyService->getPaginated(['condition' => 'Baik']);
        $this->assertCount(1, $resultCondition->items());
        $this->assertEquals('Baik', $resultCondition->items()[0]->condition);
    }

    public function test_create_saves_copy_with_auto_generated_fields(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['title' => 'Sirkulib Guide', 'category_id' => $category->id]);

        $copyData = [
            'book_id' => $book->id,
            'condition' => 'Sangat Baik',
            'acquisition_date' => '2026-05-23',
            'purchase_price' => 75000.00
        ];

        $copy = $this->bookCopyService->create($copyData);

        $this->assertInstanceOf(BookCopy::class, $copy);
        $this->assertNotEmpty($copy->copy_code);
        $this->assertEquals($copy->copy_code, $copy->barcode);
        $this->assertEquals($copy->copy_code, $copy->qr_code);
        $this->assertEquals(BookStatus::Available, $copy->status);
        $this->assertEquals('Sangat Baik', $copy->condition);
        $this->assertDatabaseHas('book_copies', [
            'id' => $copy->id,
            'condition' => 'Sangat Baik'
        ]);
    }

    public function test_bulk_generate_creates_multiple_copies(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['title' => 'Bulk Book Test', 'category_id' => $category->id]);

        $baseData = [
            'condition' => 'Baru',
            'acquisition_date' => '2026-05-23'
        ];

        $copies = $this->bookCopyService->bulkGenerate($book, 5, $baseData);

        $this->assertCount(5, $copies);
        $this->assertEquals(5, BookCopy::where('book_id', $book->id)->count());

        // Verify sequential copy codes
        $copyCodes = BookCopy::where('book_id', $book->id)->pluck('copy_code')->toArray();
        $this->assertCount(5, array_unique($copyCodes));

        foreach ($copies as $copy) {
            $this->assertEquals('Baru', $copy->condition);
            $this->assertEquals(BookStatus::Available, $copy->status);
        }
    }

    public function test_update_modifies_copy_attributes(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        $copy = BookCopy::factory()->create(['book_id' => $book->id, 'condition' => 'Baik']);

        $updated = $this->bookCopyService->update($copy, [
            'condition' => 'Rusak Sedang',
            'status' => BookStatus::Damaged->value
        ]);

        $this->assertEquals('Rusak Sedang', $updated->condition);
        $this->assertEquals(BookStatus::Damaged, $updated->status);
    }

    public function test_delete_removes_copy_successfully(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        $copy = BookCopy::factory()->create(['book_id' => $book->id, 'status' => BookStatus::Available]);

        $result = $this->bookCopyService->delete($copy);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('book_copies', ['id' => $copy->id]);
    }

    public function test_delete_fails_if_copy_is_borrowed(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        $copy = BookCopy::factory()->create(['book_id' => $book->id, 'status' => BookStatus::Borrowed]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus eksemplar yang sedang dipinjam.');

        $this->bookCopyService->delete($copy);
    }

    public function test_get_stats_returns_accurate_copy_counts(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        BookCopy::factory()->create(['book_id' => $book->id, 'status' => BookStatus::Available]);
        BookCopy::factory()->create(['book_id' => $book->id, 'status' => BookStatus::Borrowed]);
        BookCopy::factory()->create(['book_id' => $book->id, 'status' => BookStatus::Damaged]);
        BookCopy::factory()->create(['book_id' => $book->id, 'status' => BookStatus::Lost]);

        $stats = $this->bookCopyService->getStats();

        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(1, $stats['available']);
        $this->assertEquals(1, $stats['borrowed']);
        $this->assertEquals(1, $stats['damaged']);
        $this->assertEquals(1, $stats['lost']);
    }
}
