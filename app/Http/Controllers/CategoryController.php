<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'sort_dir']);
        $categories = $this->categoryService->getPaginated($filters);
        $stats = $this->categoryService->getStats();

        return view('categories.index', compact('categories', 'stats'));
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $this->categoryService->create($data);

            return back()->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified category.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $data = $request->validated();
            $this->categoryService->update($category, $data);

            return back()->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            $this->categoryService->delete($category);

            return back()->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get categories as JSON for API/Select2.
     */
    public function getAll(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        return response()->json([
            'success' => true,
            'data' => $categories->map(fn($cat) => [
                'id' => $cat->id,
                'text' => $cat->name,
                'code' => $cat->code,
                'color' => $cat->color,
            ]),
        ]);
    }
}