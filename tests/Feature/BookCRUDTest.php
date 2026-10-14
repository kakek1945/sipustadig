<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected User $librarian;
    protected User $student;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->librarian = User::factory()->librarian()->create();
        $this->student = User::factory()->create(); // defaults to Student role
        $this->category = Category::factory()->create();
    }

    public function test_librarian_can_access_books_index(): void
    {
        Book::factory()->count(3)->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->librarian)
            ->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewHas('books');
    }

    public function test_student_can_also_access_books_index(): void
    {
        // Because BookPolicy viewAny/view is true for all authenticated users
        $response = $this->actingAs($this->student)
            ->get(route('books.index'));

        $response->assertStatus(200);
    }

    public function test_librarian_can_create_book_successfully(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('cover.jpg');

        $bookData = [
            'isbn' => '978-602-0822-00-5',
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'Bentang Pustaka',
            'publish_year' => '2005',
            'category_id' => $this->category->id,
            'price' => 50000,
            'cover_image' => $file,
            'condition' => 'Baik',
        ];

        $response = $this->actingAs($this->librarian)
            ->post(route('books.store'), $bookData);

        $book = Book::where('isbn', '978-602-0822-00-5')->first();
        $this->assertNotNull($book);

        $response->assertRedirect(route('books.show', $book->id));
        $this->assertDatabaseHas('books', [
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'isbn' => '978-602-0822-00-5'
        ]);

        $this->assertNotNull($book->cover_image);
        Storage::disk('public')->assertExists($book->cover_image);
    }

    public function test_student_cannot_create_book(): void
    {
        $bookData = [
            'isbn' => '978-602-0822-00-5',
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->student)
            ->post(route('books.store'), $bookData);

        $response->assertStatus(403);
    }

    public function test_librarian_can_view_book_detail(): void
    {
        $book = Book::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->librarian)
            ->get(route('books.show', $book->id));

        $response->assertStatus(200);
        $response->assertViewHas('book');
    }

    public function test_librarian_can_update_book(): void
    {
        $book = Book::factory()->create(['title' => 'Old Title', 'category_id' => $this->category->id]);

        $response = $this->actingAs($this->librarian)
            ->put(route('books.update', $book->id), [
                'title' => 'Updated Title',
                'author' => $book->author,
                'category_id' => $this->category->id,
            ]);

        $response->assertRedirect(route('books.show', $book->id));
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_student_cannot_update_book(): void
    {
        $book = Book::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->student)
            ->put(route('books.update', $book->id), [
                'title' => 'Hack Title',
                'category_id' => $this->category->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_librarian_can_delete_book(): void
    {
        $book = Book::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->librarian)
            ->delete(route('books.destroy', $book->id));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_librarian_can_create_digital_book_with_official_link(): void
    {
        $response = $this->actingAs($this->librarian)
            ->post(route('books.store'), [
                'title' => 'Buku Digital SIBI',
                'author' => 'Kemendikdasmen',
                'category_id' => $this->category->id,
                'is_digital' => '1',
                'digital_source' => 'SIBI/Kemendikdasmen',
                'digital_url' => 'https://buku.kemendikdasmen.go.id/katalog/buku-digital-sibi',
                'digital_format' => 'PDF',
                'digital_grade_level' => 'SD',
                'digital_access_note' => 'Buka melalui situs resmi.',
            ]);

        $book = Book::where('title', 'Buku Digital SIBI')->first();
        $this->assertNotNull($book);

        $response->assertRedirect(route('books.show', $book->id));
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'is_digital' => true,
            'digital_url' => 'https://buku.kemendikdasmen.go.id/katalog/buku-digital-sibi',
        ]);
    }

    public function test_digital_book_rejects_non_official_link(): void
    {
        $response = $this->actingAs($this->librarian)
            ->post(route('books.store'), [
                'title' => 'Buku Digital Tidak Resmi',
                'author' => 'Penerbit Lain',
                'category_id' => $this->category->id,
                'is_digital' => '1',
                'digital_url' => 'https://example.com/buku.pdf',
            ]);

        $response->assertSessionHasErrors('digital_url');
        $this->assertDatabaseMissing('books', ['title' => 'Buku Digital Tidak Resmi']);
    }

    public function test_public_catalog_shows_digital_badge_and_official_link(): void
    {
        $book = Book::factory()->create([
            'category_id' => $this->category->id,
            'title' => 'Katalog Digital',
            'is_digital' => true,
            'digital_source' => 'SIBI/Kemendikdasmen',
            'digital_url' => 'https://buku.kemendikdasmen.go.id/katalog/katalog-digital',
        ]);

        $this->get(route('catalog.index', ['type' => 'digital']))
            ->assertStatus(200)
            ->assertSee('Katalog Digital')
            ->assertSee('Digital');

        $this->get(route('catalog.show', $book->id))
            ->assertStatus(200)
            ->assertSee('Buka di SIBI/Kemendikdasmen')
            ->assertSee('https://buku.kemendikdasmen.go.id/katalog/katalog-digital')
            ->assertDontSee('Ketersediaan Eksemplar');
    }
}
