<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Member;
use App\Models\User;
use App\Services\QRCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_resolve_member_scan(): void
    {
        $librarian = User::factory()->librarian()->create();
        $member = Member::factory()->create(['member_number' => 'MBR-2026-0001']);

        $this->actingAs($librarian)
            ->postJson(route('scan.resolve'), ['scan' => 'MBR-2026-0001'])
            ->assertStatus(200)
            ->assertJsonPath('found', true)
            ->assertJsonPath('type', 'member')
            ->assertJsonPath('member.id', $member->id)
            ->assertJsonPath('member.member_number', 'MBR-2026-0001');
    }

    public function test_librarian_can_resolve_book_copy_scan_from_barcode(): void
    {
        $librarian = User::factory()->librarian()->create();
        $book = Book::factory()->create(['title' => 'Buku Scan']);
        $copy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'copy_code' => 'COPY-001',
            'barcode' => 'BC123456',
        ]);

        $this->actingAs($librarian)
            ->postJson(route('scan.resolve'), ['scan' => 'BC123456'])
            ->assertStatus(200)
            ->assertJsonPath('found', true)
            ->assertJsonPath('type', 'book_copy')
            ->assertJsonPath('book_copy.id', $copy->id)
            ->assertJsonPath('book_copy.copy_code', 'COPY-001')
            ->assertJsonPath('book_copy.book.title', 'Buku Scan');
    }

    public function test_librarian_can_resolve_isbn_scan(): void
    {
        $librarian = User::factory()->librarian()->create();
        $book = Book::factory()->create([
            'isbn' => '9786020324784',
            'title' => 'Buku ISBN',
        ]);

        $this->actingAs($librarian)
            ->postJson(route('scan.resolve'), ['scan' => '9786020324784'])
            ->assertStatus(200)
            ->assertJsonPath('found', true)
            ->assertJsonPath('type', 'isbn')
            ->assertJsonPath('book.id', $book->id)
            ->assertJsonPath('book.title', 'Buku ISBN');
    }

    public function test_librarian_can_resolve_standard_qr_payload(): void
    {
        $librarian = User::factory()->librarian()->create();
        $member = Member::factory()->create(['member_number' => 'MBR-QR-001']);
        $payload = app(QRCodeService::class)->encode(app(QRCodeService::class)->memberPayload($member));

        $this->actingAs($librarian)
            ->postJson(route('scan.resolve'), ['scan' => $payload])
            ->assertStatus(200)
            ->assertJsonPath('found', true)
            ->assertJsonPath('type', 'member')
            ->assertJsonPath('member.member_number', 'MBR-QR-001');
    }

    public function test_qr_service_builds_payloads_for_loan_return_and_inventory(): void
    {
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'copy_code' => 'INV-001',
            'barcode' => 'INV-BC-001',
        ]);
        $loan = Loan::factory()->create(['loan_number' => 'LN260101-0001']);
        $loanItem = LoanItem::create([
            'loan_id' => $loan->id,
            'book_copy_id' => $copy->id,
            'due_date' => now()->addDays(7),
            'status' => 'borrowed',
        ]);
        $service = app(QRCodeService::class);

        $this->assertSame('LN260101-0001', $service->loanPayload($loan)['loan_number']);
        $this->assertSame('INV-001', $service->returnPayload($loanItem)['copy_code']);
        $this->assertSame('INV-BC-001', $service->inventoryPayload($copy)['barcode']);
    }

    public function test_librarian_can_access_member_print_card_page(): void
    {
        $librarian = User::factory()->librarian()->create();
        $member = Member::factory()->create(['member_number' => 'MBR-CARD-001']);

        $this->actingAs($librarian)
            ->get(route('members.print-card', $member))
            ->assertStatus(200)
            ->assertSee('MBR-CARD-001')
            ->assertSee('qrPayload');
    }

    public function test_librarian_can_open_quick_scan_page(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)
            ->get(route('scan.index'))
            ->assertStatus(200)
            ->assertSee('Scan Cepat')
            ->assertSee(route('scan.resolve'));
    }

    public function test_scan_resolver_returns_not_found_for_unknown_scan(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)
            ->postJson(route('scan.resolve'), ['scan' => 'UNKNOWN-SCAN'])
            ->assertStatus(404)
            ->assertJsonPath('found', false);
    }
}
