<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportsSettingsAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_view_reports_and_export_csv(): void
    {
        $librarian = User::factory()->librarian()->create();
        Book::factory()->create(['title' => 'Katalog Uji']);

        $this->actingAs($librarian)
            ->get(route('reports.index'))
            ->assertStatus(200)
            ->assertSee('Katalog Lengkap');

        $this->actingAs($librarian)
            ->get(route('reports.show', 'catalog_report'))
            ->assertStatus(200)
            ->assertSee('Katalog Uji');

        $this->actingAs($librarian)
            ->get(route('reports.export', ['type' => 'catalog_report', 'format' => 'csv']))
            ->assertStatus(200);
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'loan' => [
                'max_borrow_days' => 10,
                'max_books' => 4,
                'max_extensions' => 2,
                'max_overdue_days' => 20,
            ],
            'fine' => [
                'daily_rate' => 1500,
                'max_amount' => 75000,
                'waiver_requires_reason' => 1,
            ],
            'member' => [
                'max_unpaid_fines' => 50000,
                'card_validity_years' => 3,
            ],
            'library' => [
                'name' => 'Perpustakaan Uji',
                'school_name' => 'SMP Uji Literasi',
                'npsn' => '12345678',
                'address' => 'Jl. Sekolah',
                'phone' => '021',
                'email' => 'library@example.test',
                'service_hours' => 'Senin-Jumat 07.30-14.00',
                'vision' => 'Visi literasi sekolah',
                'mission' => 'Misi satu',
                'goals' => 'Tujuan satu',
                'sop_summary' => 'Ringkasan SOP layanan',
                'sop_flow' => 'Cari buku\nPinjam ke petugas',
                'sop_document_url' => 'https://example.test/sop.pdf',
                'principal_name' => 'Kepala Uji',
                'principal_title' => 'Kepala Sekolah',
                'principal_speech' => 'Selamat membaca.',
            ],
            'system' => [
                'auto_assign_barcode' => 1,
                'auto_assign_qrcode' => 1,
            ],
        ];

        $this->actingAs($admin)
            ->put(route('settings.update'), $payload)
            ->assertRedirect();

        $this->assertSame('Perpustakaan Uji', app('settings')->get('library.name'));
        $this->assertSame('SMP Uji Literasi', app('settings')->get('library.school_name'));
        $this->assertSame('Visi literasi sekolah', app('settings')->get('library.vision'));
        $this->assertSame('Kepala Uji', app('settings')->get('library.principal_name'));
        $this->assertSame(10.0, app('settings')->get('loan.max_borrow_days'));
    }

    public function test_public_homepage_shows_library_profile_and_public_stats(): void
    {
        app('settings')->set('library.name', 'Perpustakaan Literasi');
        app('settings')->set('library.school_name', 'SMP Negeri Uji');
        app('settings')->set('library.npsn', '87654321');
        app('settings')->set('library.vision', 'Pusat literasi sekolah');
        app('settings')->set('library.principal_name', 'Ibu Kepala');
        app('settings')->set('library.principal_speech', 'Selamat datang di pustaka.');
        app('settings')->set('library.sop_summary', 'Ikuti alur layanan pustaka.');

        $category = Category::factory()->create(['name' => 'Referensi']);
        $book = Book::factory()->create([
            'title' => 'Buku Profil Publik',
            'category_id' => $category->id,
        ]);
        BookCopy::factory()->create(['book_id' => $book->id, 'status' => 'available']);

        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee('Perpustakaan Literasi')
            ->assertSee('SMP Negeri Uji')
            ->assertSee('87654321')
            ->assertSee('Pusat literasi sekolah')
            ->assertSee('Ibu Kepala')
            ->assertSee('Buku Profil Publik')
            ->assertSee('Eksemplar Tersedia')
            ->assertDontSee('Denda');
    }

    public function test_admin_can_upload_principal_photo_for_public_homepage(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $payload = [
            'loan' => [
                'max_borrow_days' => 7,
                'max_books' => 3,
                'max_extensions' => 2,
                'max_overdue_days' => 14,
            ],
            'fine' => [
                'daily_rate' => 1000,
                'max_amount' => 50000,
                'waiver_requires_reason' => 1,
            ],
            'member' => [
                'max_unpaid_fines' => 50000,
                'card_validity_years' => 3,
            ],
            'library' => [
                'name' => 'Perpustakaan Foto',
                'school_name' => 'Sekolah Foto',
                'npsn' => '123',
                'address' => 'Jl. Foto',
                'phone' => '021',
                'email' => 'foto@example.test',
                'service_hours' => 'Senin-Jumat',
                'vision' => 'Visi foto',
                'mission' => 'Misi foto',
                'goals' => 'Tujuan foto',
                'sop_summary' => 'SOP foto',
                'sop_flow' => 'Alur foto',
                'sop_document_url' => 'https://example.test/sop.pdf',
                'principal_name' => 'Kepala Foto',
                'principal_title' => 'Kepala Sekolah',
                'principal_speech' => 'Sambutan foto',
                'principal_photo' => UploadedFile::fake()->image('principal.jpg'),
            ],
            'system' => [
                'auto_assign_barcode' => 1,
                'auto_assign_qrcode' => 1,
            ],
        ];

        $this->actingAs($admin)
            ->put(route('settings.update'), $payload)
            ->assertRedirect();

        $photo = app('settings')->get('library.principal_photo');

        $this->assertNotEmpty($photo);
        Storage::disk('public')->assertExists($photo);
    }

    public function test_librarian_can_view_audit_log(): void
    {
        $librarian = User::factory()->librarian()->create();
        $log = AuditLog::create([
            'user_id' => $librarian->id,
            'action' => 'create',
            'model_type' => 'Book',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'new_values' => ['title' => 'Audit Book'],
            'created_at' => now(),
        ]);

        $this->actingAs($librarian)
            ->get(route('audit-logs.index'))
            ->assertStatus(200)
            ->assertSee('Book');

        $this->actingAs($librarian)
            ->get(route('audit-logs.show', $log->id))
            ->assertStatus(200)
            ->assertSee('Audit Book');
    }
}
