<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::query()
            ->with('category')
            ->withCount([
                'copies as total_copies',
                'copies as available_copies' => fn (Builder $query) => $query->where('status', BookStatus::Available->value),
            ])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function (Builder $query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                        ->orWhere('author', 'like', "%{$q}%")
                        ->orWhere('publisher', 'like', "%{$q}%")
                        ->orWhere('isbn', 'like', "%{$q}%")
                        ->orWhere('rack_location', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category_id', $request->input('category'));
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                if ($request->input('type') === 'digital') {
                    $query->where('is_digital', true);
                } elseif ($request->input('type') === 'physical') {
                    $query->where('is_digital', false);
                }
            })
            ->when($request->filled('availability'), function ($query) use ($request) {
                if ($request->input('availability') === 'available') {
                    $query->where(function (Builder $query) {
                        $query->where('is_digital', true)
                            ->orWhereHas('copies', fn (Builder $query) => $query->where('status', BookStatus::Available->value));
                    });
                }
            })
            ->orderBy('title')
            ->paginate(12);

        $categories = Category::orderBy('sort_order')->orderBy('name')->get();

        return view('catalog.index', compact('books', 'categories'));
    }

    public function show(string $id): View
    {
        $book = Book::query()
            ->with(['category', 'copies' => fn ($query) => $query->orderBy('copy_code')])
            ->withCount([
                'copies as total_copies',
                'copies as available_copies' => fn (Builder $query) => $query->where('status', BookStatus::Available->value),
            ])
            ->findOrFail($id);

        return view('catalog.show', compact('book'));
    }
}
