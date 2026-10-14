<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories.
     */
    public function getAll(): Collection
    {
        return Category::orderBy('sort_order')->orderBy('name')->get();
    }

    /**
     * Get paginated categories.
     */
    public function getPaginated(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Category::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $query->orderBy('sort_order')->orderBy('name');

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        // Set sort order to max + 1 if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = Category::max('sort_order') + 1;
        }

        return Category::create($data);
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): bool
    {
        // Check if category has books
        if ($category->books()->count() > 0) {
            throw new \Exception('Tidak dapat menghapus kategori yang masih memiliki buku.');
        }

        return $category->delete();
    }

    /**
     * Get category statistics.
     */
    public function getStats(): array
    {
        return [
            'total' => Category::count(),
            'with_books' => Category::has('books')->count(),
        ];
    }

    /**
     * Update sort order for multiple categories.
     */
    public function updateSortOrder(array $order): void
    {
        foreach ($order as $index => $categoryId) {
            Category::where('id', $categoryId)->update(['sort_order' => $index]);
        }
    }
}