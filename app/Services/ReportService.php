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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function types(): array
    {
        return [
            'loan_summary' => 'Ringkasan Peminjaman',
            'loan_history' => 'Riwayat Peminjaman',
            'book_availability' => 'Ketersediaan Buku',
            'member_activity' => 'Aktivitas Anggota',
            'fine_report' => 'Laporan Denda',
            'overdue_report' => 'Laporan Terlambat',
            'catalog_report' => 'Katalog Lengkap',
            'monthly_report' => 'Laporan Bulanan',
            'annual_report' => 'Laporan Tahunan',
            'damage_report' => 'Laporan Kerusakan',
            'popular_books' => 'Buku Populer',
        ];
    }

    public function get(string $type, array $filters = []): array
    {
        abort_unless(array_key_exists($type, $this->types()), 404);

        return match ($type) {
            'loan_summary' => $this->loanSummary($filters),
            'loan_history' => $this->loanHistory($filters),
            'book_availability' => $this->bookAvailability($filters),
            'member_activity' => $this->memberActivity($filters),
            'fine_report' => $this->fineReport($filters),
            'overdue_report' => $this->overdueReport($filters),
            'catalog_report' => $this->catalogReport($filters),
            'monthly_report' => $this->monthlyReport($filters),
            'annual_report' => $this->annualReport($filters),
            'damage_report' => $this->damageReport($filters),
            'popular_books' => $this->popularBooks($filters),
        };
    }

    protected function loanSummary(array $filters): array
    {
        $query = $this->filterLoanDates(Loan::query(), $filters);

        return [
            'columns' => ['Metrik', 'Jumlah'],
            'rows' => collect([
                ['Peminjaman aktif', (clone $query)->whereIn('status', [LoanStatus::Active->value, LoanStatus::Extended->value])->count()],
                ['Dikembalikan', (clone $query)->where('status', LoanStatus::Returned->value)->count()],
                ['Terlambat', (clone $query)->where('status', LoanStatus::Overdue->value)->count()],
                ['Total transaksi', (clone $query)->count()],
            ]),
            'summary' => ['total' => (clone $query)->count()],
        ];
    }

    protected function loanHistory(array $filters): array
    {
        $rows = $this->filterLoanDates(Loan::with(['member', 'items.bookCopy.book']), $filters)
            ->latest('loan_date')
            ->limit(500)
            ->get()
            ->map(fn (Loan $loan) => [
                $loan->loan_number,
                $loan->member?->name,
                $loan->items->pluck('bookCopy.book.title')->filter()->join(', '),
                $loan->loan_date?->format('d M Y'),
                $loan->due_date?->format('d M Y'),
                $loan->status->label(),
            ]);

        return [
            'columns' => ['No. Pinjam', 'Anggota', 'Buku', 'Tanggal', 'Jatuh Tempo', 'Status'],
            'rows' => $rows,
            'summary' => ['total' => $rows->count()],
        ];
    }

    protected function bookAvailability(array $filters): array
    {
        $rows = Book::with('category')
            ->withCount([
                'copies as total_copies',
                'copies as available_copies' => fn ($query) => $query->where('status', BookStatus::Available->value),
                'copies as borrowed_copies' => fn ($query) => $query->where('status', BookStatus::Borrowed->value),
            ])
            ->orderBy('title')
            ->get()
            ->map(fn (Book $book) => [
                $book->title,
                $book->author,
                $book->category?->name ?? '-',
                $book->rack_location ?? '-',
                $book->available_copies,
                $book->borrowed_copies,
                $book->total_copies,
            ]);

        return [
            'columns' => ['Judul', 'Pengarang', 'Kategori', 'Rak', 'Tersedia', 'Dipinjam', 'Total'],
            'rows' => $rows,
            'summary' => ['total' => $rows->count()],
        ];
    }

    protected function memberActivity(array $filters): array
    {
        $rows = Member::query()
            ->withCount(['loans', 'fines'])
            ->withSum('fines as unpaid_fines', 'amount')
            ->orderBy('name')
            ->get()
            ->map(fn (Member $member) => [
                $member->member_number,
                $member->name,
                $member->type->label(),
                $member->class ?? '-',
                $member->loans_count,
                $member->fines_count,
            ]);

        return [
            'columns' => ['No. Anggota', 'Nama', 'Tipe', 'Kelas', 'Peminjaman', 'Denda'],
            'rows' => $rows,
            'summary' => ['total' => $rows->count()],
        ];
    }

    protected function fineReport(array $filters): array
    {
        $rows = $this->filterFineDates(Fine::with(['member', 'loan']), $filters)
            ->latest()
            ->get()
            ->map(fn (Fine $fine) => [
                $fine->fine_number,
                $fine->member?->name,
                $fine->loan?->loan_number,
                $fine->late_days,
                (float) $fine->amount,
                (float) $fine->paid_amount,
                $fine->status->label(),
            ]);

        return [
            'columns' => ['No. Denda', 'Anggota', 'Pinjam', 'Hari', 'Jumlah', 'Dibayar', 'Status'],
            'rows' => $rows,
            'summary' => ['total_amount' => $rows->sum(fn ($row) => $row[4])],
        ];
    }

    protected function overdueReport(array $filters): array
    {
        $rows = LoanItem::with(['loan.member', 'bookCopy.book'])
            ->where('status', 'borrowed')
            ->whereDate('due_date', '<', now()->toDateString())
            ->orderBy('due_date')
            ->get()
            ->map(fn (LoanItem $item) => [
                $item->loan?->loan_number,
                $item->loan?->member?->name,
                $item->bookCopy?->book?->title,
                $item->bookCopy?->copy_code,
                $item->due_date?->format('d M Y'),
                $item->daysOverdue(),
            ]);

        return [
            'columns' => ['No. Pinjam', 'Anggota', 'Buku', 'Eksemplar', 'Jatuh Tempo', 'Hari Terlambat'],
            'rows' => $rows,
            'summary' => ['total' => $rows->count()],
        ];
    }

    protected function catalogReport(array $filters): array
    {
        $rows = Book::with('category')
            ->orderBy('title')
            ->get()
            ->map(fn (Book $book) => [
                $book->isbn ?? '-',
                $book->title,
                $book->author,
                $book->publisher ?? '-',
                $book->publish_year ?? '-',
                $book->category?->name ?? '-',
                $book->rack_location ?? '-',
            ]);

        return [
            'columns' => ['ISBN', 'Judul', 'Pengarang', 'Penerbit', 'Tahun', 'Kategori', 'Rak'],
            'rows' => $rows,
            'summary' => ['total' => $rows->count()],
        ];
    }

    protected function monthlyReport(array $filters): array
    {
        $year = (int) ($filters['year'] ?? now()->year);
        $rows = Loan::query()
            ->selectRaw('MONTH(loan_date) as month, COUNT(*) as loans')
            ->whereYear('loan_date', $year)
            ->groupBy('month')
            ->pluck('loans', 'month');

        $data = collect(range(1, 12))->map(fn (int $month) => [
            \Carbon\Carbon::create($year, $month, 1)->format('F'),
            (int) ($rows[$month] ?? 0),
        ]);

        return [
            'columns' => ['Bulan', 'Peminjaman'],
            'rows' => $data,
            'summary' => ['year' => $year, 'total' => $data->sum(fn ($row) => $row[1])],
        ];
    }

    protected function annualReport(array $filters): array
    {
        $rows = Loan::query()
            ->selectRaw('YEAR(loan_date) as year, COUNT(*) as loans')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(fn ($row) => [$row->year, $row->loans]);

        return [
            'columns' => ['Tahun', 'Peminjaman'],
            'rows' => $rows,
            'summary' => ['total' => $rows->sum(fn ($row) => $row[1])],
        ];
    }

    protected function damageReport(array $filters): array
    {
        $rows = BookCopy::with('book')
            ->whereIn('status', [BookStatus::Damaged->value, BookStatus::Lost->value])
            ->orderBy('status')
            ->get()
            ->map(fn (BookCopy $copy) => [
                $copy->copy_code,
                $copy->book?->title,
                $copy->status->label(),
                $copy->condition ?? '-',
                $copy->notes ?? '-',
            ]);

        return [
            'columns' => ['Kode', 'Buku', 'Status', 'Kondisi', 'Catatan'],
            'rows' => $rows,
            'summary' => ['total' => $rows->count()],
        ];
    }

    protected function popularBooks(array $filters): array
    {
        $rows = Book::with('category')
            ->withCount(['copies as loan_count' => fn ($query) => $query->join('loan_items', 'book_copies.id', '=', 'loan_items.book_copy_id')])
            ->orderByDesc('loan_count')
            ->limit(50)
            ->get()
            ->map(fn (Book $book) => [
                $book->title,
                $book->author,
                $book->category?->name ?? '-',
                $book->loan_count,
            ]);

        return [
            'columns' => ['Judul', 'Pengarang', 'Kategori', 'Dipinjam'],
            'rows' => $rows,
            'summary' => ['total' => $rows->count()],
        ];
    }

    protected function filterLoanDates(Builder $query, array $filters): Builder
    {
        return $query
            ->when(!empty($filters['start_date']), fn ($query) => $query->whereDate('loan_date', '>=', $filters['start_date']))
            ->when(!empty($filters['end_date']), fn ($query) => $query->whereDate('loan_date', '<=', $filters['end_date']));
    }

    protected function filterFineDates(Builder $query, array $filters): Builder
    {
        return $query
            ->when(!empty($filters['start_date']), fn ($query) => $query->whereDate('created_at', '>=', $filters['start_date']))
            ->when(!empty($filters['end_date']), fn ($query) => $query->whereDate('created_at', '<=', $filters['end_date']));
    }
}
