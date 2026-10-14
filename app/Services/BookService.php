<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookService
{
    /**
     * Get paginated books with filters.
     */
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = Book::query()->with('category');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (($filters['type'] ?? '') === 'digital') {
            $query->where('is_digital', true);
        } elseif (($filters['type'] ?? '') === 'physical') {
            $query->where('is_digital', false);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';

        // Ensure valid sort fields to prevent SQL injection or errors
        $allowedSorts = ['title', 'author', 'publisher', 'publish_year', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Get all books for export.
     */
    public function getAllForExport(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Book::query()->with(['category', 'copies']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->orderBy('title')->get();
    }

    /**
     * Create a new book.
     */
    public function create(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            return Book::create($data);
        });
    }

    /**
     * Update a book.
     */
    public function update(Book $book, array $data): Book
    {
        return DB::transaction(function () use ($book, $data) {
            $book->update($data);
            return $book->fresh();
        });
    }

    /**
     * Delete a book.
     */
    public function delete(Book $book): bool
    {
        return DB::transaction(function () use ($book) {
            // Check if any book copy is currently borrowed
            $hasBorrowedCopies = $book->copies()->where('status', 'borrowed')->exists();
            if ($hasBorrowedCopies) {
                throw new \Exception('Tidak dapat menghapus buku karena masih ada eksemplar yang sedang dipinjam.');
            }

            // Delete cover image if it exists
            $disk = config('filesystems.uploads');
            if ($book->cover_image && Storage::disk($disk)->exists($book->cover_image)) {
                Storage::disk($disk)->delete($book->cover_image);
            }

            // This will delete associated book copies via database cascade constraint if configured,
            // but let's delete them explicitly or let cascade handle it. The migration says ->cascadeOnDelete() on book_id.
            return $book->delete();
        });
    }

    /**
     * Get book statistics.
     */
    public function getStats(): array
    {
        return [
            'total_titles' => Book::count(),
            'digital_titles' => Book::where('is_digital', true)->count(),
            'physical_titles' => Book::where('is_digital', false)->count(),
            'total_copies' => BookCopy::count(),
            'available_copies' => BookCopy::where('status', 'available')->count(),
            'borrowed_copies' => BookCopy::where('status', 'borrowed')->count(),
        ];
    }
}
