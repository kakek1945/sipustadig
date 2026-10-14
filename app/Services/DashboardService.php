<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Enums\FineStatus;
use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function stats(): array
    {
        return [
            'total_members' => Member::count(),
            'active_members' => Member::where('status', 'active')->count(),
            'total_books' => Book::count(),
            'total_copies' => BookCopy::count(),
            'available_copies' => BookCopy::where('status', BookStatus::Available->value)->count(),
            'active_loans' => Loan::whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value])->count(),
            'overdue_loans' => Loan::where(function ($query) {
                $query->where('status', LoanStatus::Overdue->value)
                    ->orWhere(function ($query) {
                        $query->whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value])
                            ->whereDate('due_date', '<', now()->toDateString());
                    });
            })->count(),
            'returned_this_month' => Loan::where('status', LoanStatus::Returned->value)
                ->whereMonth('return_date', now()->month)
                ->whereYear('return_date', now()->year)
                ->count(),
            'pending_fines' => (float) Fine::where('status', FineStatus::Pending->value)
                ->selectRaw('COALESCE(SUM(amount - paid_amount), 0) as total')
                ->value('total'),
            'unpaid_fines_count' => Fine::where('status', FineStatus::Pending->value)->count(),
        ];
    }

    public function recentLoans(int $limit = 8): Collection
    {
        return Loan::with(['member', 'items.bookCopy.book'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function popularBooks(int $limit = 8): Collection
    {
        return Book::query()
            ->with('category')
            ->withCount([
                'copies',
                'copies as available_copies_count' => fn ($query) => $query->where('status', BookStatus::Available->value),
                'copies as loan_count' => fn ($query) => $query->join('loan_items', 'book_copies.id', '=', 'loan_items.book_copy_id'),
            ])
            ->orderByDesc('loan_count')
            ->orderBy('title')
            ->limit($limit)
            ->get();
    }

    public function loanTrend(int $days = 7): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $rows = Loan::query()
            ->selectRaw('DATE(loan_date) as date, COUNT(*) as total')
            ->whereDate('loan_date', '>=', $start->toDateString())
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $values = [];

        for ($index = 0; $index < $days; $index++) {
            $date = $start->copy()->addDays($index);
            $key = $date->toDateString();
            $labels[] = $date->format('d M');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function categoryDistribution(): array
    {
        $rows = Book::query()
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Tanpa Kategori') as label, COUNT(books.id) as total")
            ->groupBy(DB::raw("COALESCE(categories.name, 'Tanpa Kategori')"))
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return [
            'labels' => $rows->pluck('label')->all(),
            'values' => $rows->pluck('total')->map(fn ($value) => (int) $value)->all(),
        ];
    }

    public function principalSummary(): array
    {
        $stats = $this->stats();

        return [
            'circulation_rate' => $stats['total_copies'] > 0
                ? round(($stats['active_loans'] / $stats['total_copies']) * 100, 1)
                : 0,
            'availability_rate' => $stats['total_copies'] > 0
                ? round(($stats['available_copies'] / $stats['total_copies']) * 100, 1)
                : 0,
            'overdue_rate' => $stats['active_loans'] > 0
                ? round(($stats['overdue_loans'] / $stats['active_loans']) * 100, 1)
                : 0,
        ];
    }
}
