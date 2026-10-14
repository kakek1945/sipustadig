<?php

namespace Tests\Feature;

use App\Actions\CreateLoan;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_dashboard_shows_real_stats_and_widgets(): void
    {
        $librarian = User::factory()->librarian()->create();
        $member = Member::factory()->create();
        $book = Book::factory()->create(['category_id' => Category::factory()->create()->id, 'title' => 'Buku Populer']);
        $copy = BookCopy::factory()->available()->create(['book_id' => $book->id]);

        app(CreateLoan::class)->handle($member, [$copy->id], $librarian, ['loan_date' => now()->toDateString()]);

        $this->actingAs($librarian)
            ->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Peminjaman Terbaru')
            ->assertSee('Buku Populer')
            ->assertViewHasAll(['stats', 'recent_loans', 'popular_books', 'loan_trend']);
    }

    public function test_principal_dashboard_shows_summary_metrics(): void
    {
        $principal = User::factory()->create([
            'role' => \App\Enums\Role::Principal,
        ]);

        $this->actingAs($principal)
            ->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Tingkat Sirkulasi')
            ->assertViewHas('principal_summary');
    }
}
