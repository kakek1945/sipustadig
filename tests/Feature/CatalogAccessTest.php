<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_access_catalog_index(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Fisika Dasar',
        ]);
        BookCopy::factory()->available()->create(['book_id' => $book->id]);

        $this->get(route('catalog.index'))
            ->assertStatus(200)
            ->assertSee('Fisika Dasar');
    }

    public function test_catalog_search_and_available_filter(): void
    {
        $category = Category::factory()->create();
        $availableBook = Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Matematika Terapan',
        ]);
        $borrowedBook = Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Matematika Lanjutan',
        ]);

        BookCopy::factory()->available()->create(['book_id' => $availableBook->id]);
        BookCopy::factory()->borrowed()->create(['book_id' => $borrowedBook->id]);

        $this->get(route('catalog.index', [
            'q' => 'Matematika',
            'availability' => 'available',
        ]))
            ->assertStatus(200)
            ->assertSee('Matematika Terapan')
            ->assertDontSee('Matematika Lanjutan');
    }

    public function test_public_user_can_view_catalog_detail_with_availability(): void
    {
        $book = Book::factory()->create(['title' => 'Bahasa Indonesia']);
        BookCopy::factory()->create([
            'book_id' => $book->id,
            'status' => BookStatus::Available->value,
            'copy_code' => 'BI-001',
        ]);

        $this->get(route('catalog.show', $book->id))
            ->assertStatus(200)
            ->assertSee('Bahasa Indonesia')
            ->assertSee('BI-001')
            ->assertSee('Tersedia');
    }
}
