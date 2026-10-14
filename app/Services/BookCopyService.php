<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BookCopyService
{
    /**
     * Get paginated book copies with filters.
     */
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = BookCopy::query()->with('book');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('copy_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhereHas('book', function ($bkQuery) use ($search) {
                      $bkQuery->where('title', 'like', "%{$search}%")
                              ->orWhere('author', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';

        $allowedSorts = ['copy_code', 'status', 'condition', 'acquisition_date', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Create a new book copy.
     */
    public function create(array $data): BookCopy
    {
        return DB::transaction(function () use ($data) {
            $book = Book::findOrFail($data['book_id']);

            // Auto-generate copy code if not provided
            if (empty($data['copy_code'])) {
                $data['copy_code'] = BookCopy::generateCopyCode($book);
            }

            // Set barcode/qr_code to copy_code if not provided
            $data['barcode'] = $data['barcode'] ?? $data['copy_code'];
            $data['qr_code'] = $data['qr_code'] ?? $data['copy_code'];

            // Set status default to available
            $data['status'] = $data['status'] ?? BookStatus::Available->value;

            return BookCopy::create($data);
        });
    }

    /**
     * Bulk generate book copies.
     */
    public function bulkGenerate(Book $book, int $count, array $baseData = []): array
    {
        return DB::transaction(function () use ($book, $count, $baseData) {
            $createdCopies = [];

            for ($i = 0; $i < $count; $i++) {
                // Generate sequential copy code using dynamic query in model generator
                $copyCode = BookCopy::generateCopyCode($book);

                $copyData = array_merge($baseData, [
                    'book_id' => $book->id,
                    'copy_code' => $copyCode,
                    'barcode' => $copyCode,
                    'qr_code' => $copyCode,
                    'status' => BookStatus::Available->value,
                    'condition' => $baseData['condition'] ?? 'Baik',
                    'acquisition_date' => $baseData['acquisition_date'] ?? now()->toDateString(),
                ]);

                $createdCopies[] = BookCopy::create($copyData);
            }

            return $createdCopies;
        });
    }

    /**
     * Update a book copy.
     */
    public function update(BookCopy $copy, array $data): BookCopy
    {
        return DB::transaction(function () use ($copy, $data) {
            // Keep book_id and copy_code from being changed unless explicitly needed
            unset($data['book_id']);
            unset($data['copy_code']);

            $copy->update($data);
            return $copy->fresh();
        });
    }

    /**
     * Delete a book copy.
     */
    public function delete(BookCopy $copy): bool
    {
        return DB::transaction(function () use ($copy) {
            // Check if copy is currently borrowed
            if ($copy->status === BookStatus::Borrowed) {
                throw new \Exception('Tidak dapat menghapus eksemplar yang sedang dipinjam.');
            }

            // Check if there are active loans
            if ($copy->activeLoan()) {
                throw new \Exception('Tidak dapat menghapus eksemplar yang memiliki peminjaman aktif.');
            }

            return $copy->delete();
        });
    }

    /**
     * Get copies statistics.
     */
    public function getStats(): array
    {
        return [
            'total' => BookCopy::count(),
            'available' => BookCopy::where('status', BookStatus::Available->value)->count(),
            'borrowed' => BookCopy::where('status', BookStatus::Borrowed->value)->count(),
            'damaged' => BookCopy::where('status', BookStatus::Damaged->value)->count(),
            'lost' => BookCopy::where('status', BookStatus::Lost->value)->count(),
        ];
    }
}
