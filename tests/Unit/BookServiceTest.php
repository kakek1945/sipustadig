<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BookService $bookService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookService = new BookService();
    }

    public function test_get_paginated_returns_paginated_books(): void
    {
        $category = Category::factory()->create();
        Book::factory()->count(20)->create(['category_id' => $category->id]);

        $result = $this->bookService->getPaginated(['per_page' => 15]);

        $this->assertCount(15, $result->items());
        $this->assertEquals(20, $result->total());
    }

    public function test_get_paginated_filters_by_search_query(): void
    {
        $category = Category::factory()->create();

        Book::factory()->create([
            'title' => 'Unique Title X',
            'author' => 'Author A',
            'category_id' => $category->id
        ]);
        Book::factory()->create([
            'title' => 'Normal Title Y',
            'author' => 'Author Unique B',
            'category_id' => $category->id
        ]);
        Book::factory()->create([
            'title' => 'Other Title Z',
            'author' => 'Author C',
            'category_id' => $category->id
        ]);

        // Search by title
        $searchTitle = $this->bookService->getPaginated(['search' => 'Unique Title']);
        $this->assertCount(1, $searchTitle->items());
        $this->assertEquals('Unique Title X', $searchTitle->items()[0]->title);

        // Search by author
        $searchAuthor = $this->bookService->getPaginated(['search' => 'Unique B']);
        $this->assertCount(1, $searchAuthor->items());
        $this->assertEquals('Normal Title Y', $searchAuthor->items()[0]->title);
    }

    public function test_get_paginated_filters_by_category(): void
    {
        $catA = Category::factory()->create();
        $catB = Category::factory()->create();

        Book::factory()->count(5)->create(['category_id' => $catA->id]);
        Book::factory()->count(3)->create(['category_id' => $catB->id]);

        $result = $this->bookService->getPaginated(['category_id' => $catB->id]);

        $this->assertCount(3, $result->items());
    }

    public function test_create_saves_book_to_database(): void
    {
        $category = Category::factory()->create();
        $bookData = [
            'isbn' => '978-602-0822-00-5',
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'Bentang Pustaka',
            'publish_year' => '2005',
            'category_id' => $category->id,
            'price' => 50000.00
        ];

        $book = $this->bookService->create($bookData);

        $this->assertInstanceOf(Book::class, $book);
        $this->assertDatabaseHas('books', [
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'isbn' => '978-602-0822-00-5'
        ]);
    }

    public function test_update_modifies_existing_book(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['title' => 'Old Title', 'category_id' => $category->id]);

        $updatedBook = $this->bookService->update($book, ['title' => 'New Title']);

        $this->assertEquals('New Title', $updatedBook->title);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'New Title'
        ]);
    }

    public function test_delete_removes_book_successfully(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $result = $this->bookService->delete($book);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_delete_fails_if_any_book_copy_is_borrowed(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        BookCopy::factory()->create([
            'book_id' => $book->id,
            'status' => 'borrowed',
            'copy_code' => 'CO-001'
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus buku karena masih ada eksemplar yang sedang dipinjam.');

        $this->bookService->delete($book);
    }

    public function test_get_stats_returns_accurate_counts(): void
    {
        $category = Category::factory()->create();
        $book1 = Book::factory()->create(['category_id' => $category->id]);
        $book2 = Book::factory()->create(['category_id' => $category->id]);

        BookCopy::factory()->create(['book_id' => $book1->id, 'status' => 'available', 'copy_code' => 'C1']);
        BookCopy::factory()->create(['book_id' => $book1->id, 'status' => 'borrowed', 'copy_code' => 'C2']);
        BookCopy::factory()->create(['book_id' => $book2->id, 'status' => 'available', 'copy_code' => 'C3']);

        $stats = $this->bookService->getStats();

        $this->assertEquals(2, $stats['total_titles']);
        $this->assertEquals(3, $stats['total_copies']);
        $this->assertEquals(2, $stats['available_copies']);
        $this->assertEquals(1, $stats['borrowed_copies']);
    }
}
