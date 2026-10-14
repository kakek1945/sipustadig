<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {}

    /**
     * Display a listing of books.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'type', 'sort_by', 'sort_dir', 'per_page']);

        $books = $this->bookService->getPaginated($filters);
        $categories = Category::orderBy('name')->get();
        $stats = $this->bookService->getStats();

        return view('books.index', compact('books', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(): View
    {
        \Illuminate\Support\Facades\Gate::authorize('create', Book::class);

        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('categories'));
    }

    /**
     * Store a newly created book.
     */
    public function store(BookRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['is_digital'] = $request->boolean('is_digital');

            if (!$data['is_digital']) {
                $data['digital_source'] = null;
                $data['digital_url'] = null;
                $data['digital_format'] = null;
                $data['digital_grade_level'] = null;
                $data['digital_access_note'] = null;
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('covers/books', config('filesystems.default'));
            }

            $book = $this->bookService->create($data);

            return redirect()
                ->route('books.show', $book->id)
                ->with('success', 'Buku berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified book.
     */
    public function show(string $id): View
    {
        $book = Book::with(['category', 'copies' => function ($query) {
            $query->orderBy('copy_code');
        }])->findOrFail($id);

        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(string $id): View
    {
        $book = Book::findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('update', $book);

        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book.
     */
    public function update(BookRequest $request, string $id): RedirectResponse
    {
        try {
            $book = Book::findOrFail($id);
            $data = $request->validated();
            $data['is_digital'] = $request->boolean('is_digital');

            if (!$data['is_digital']) {
                $data['digital_source'] = null;
                $data['digital_url'] = null;
                $data['digital_format'] = null;
                $data['digital_grade_level'] = null;
                $data['digital_access_note'] = null;
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                // Delete old cover if exists
                $disk = config('filesystems.default');
                if ($book->cover_image && Storage::disk($disk)->exists($book->cover_image)) {
                    Storage::disk($disk)->delete($book->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('covers/books', $disk);
            }

            $this->bookService->update($book, $data);

            return redirect()
                ->route('books.show', $book->id)
                ->with('success', 'Data buku berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified book.
     */
    public function destroy(string $id): RedirectResponse
    {
        $book = Book::findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('delete', $book);

        try {
            $this->bookService->delete($book);

            return redirect()
                ->route('books.index')
                ->with('success', 'Buku berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
