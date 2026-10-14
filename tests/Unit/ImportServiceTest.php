<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_members_from_csv(): void
    {
        $file = UploadedFile::fake()->createWithContent('members.csv', implode("\n", [
            'name,email,type,nisn,employee_number,class,year_level,phone,address,gender',
            'Budi,budi@example.test,student,1234567890,,X IPA 1,10,0812,Jalan,male',
        ]));

        $result = app(ImportService::class)->import('members', $file);

        $this->assertSame(1, $result['created']);
        $this->assertDatabaseHas('members', ['email' => 'budi@example.test']);
    }

    public function test_dry_run_does_not_persist_books(): void
    {
        $file = UploadedFile::fake()->createWithContent('books.csv', implode("\n", [
            'isbn,title,author,publisher,publish_year,category_code,rack_location,price,description',
            '9786020000001,Matematika Dasar,Tim Guru,Pustaka,2024,MTK,Rak A1,50000,Buku pelajaran',
        ]));

        $result = app(ImportService::class)->import('books', $file, true);

        $this->assertSame(1, $result['valid']);
        $this->assertDatabaseMissing('books', ['isbn' => '9786020000001']);
    }

    public function test_import_copies_requires_existing_book(): void
    {
        $category = Category::factory()->create();
        Book::factory()->create(['category_id' => $category->id, 'isbn' => '9786020000002']);

        $file = UploadedFile::fake()->createWithContent('copies.csv', implode("\n", [
            'book_isbn,copy_code,barcode,status,condition,acquisition_date,purchase_price,notes',
            '9786020000002,COPY-001,COPY-001,available,Baik,2026-05-23,50000,',
        ]));

        $result = app(ImportService::class)->import('copies', $file);

        $this->assertSame(1, $result['created']);
        $this->assertDatabaseHas('book_copies', ['copy_code' => 'COPY-001']);
    }
}
