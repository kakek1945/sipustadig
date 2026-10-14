<?php

namespace Tests\Unit;

use App\Actions\CreateLoan;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_service_returns_all_report_types(): void
    {
        $types = app(ReportService::class)->types();

        $this->assertCount(11, $types);
        $this->assertArrayHasKey('loan_summary', $types);
        $this->assertArrayHasKey('popular_books', $types);
    }

    public function test_loan_history_report_contains_created_loan(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id, 'title' => 'Sejarah Nusantara']);
        $copy = BookCopy::factory()->available()->create(['book_id' => $book->id]);
        $member = Member::factory()->create(['name' => 'Ayu Lestari']);
        $librarian = User::factory()->librarian()->create();

        app(CreateLoan::class)->handle($member, [$copy->id], $librarian, [
            'loan_date' => '2026-05-23',
        ]);

        $report = app(ReportService::class)->get('loan_history', [
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]);

        $this->assertSame(['No. Pinjam', 'Anggota', 'Buku', 'Tanggal', 'Jatuh Tempo', 'Status'], $report['columns']);
        $this->assertCount(1, $report['rows']);
        $this->assertSame('Ayu Lestari', $report['rows']->first()[1]);
    }
}
