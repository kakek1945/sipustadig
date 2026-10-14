<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookCopyRequest;
use App\Models\Book;
use App\Models\BookCopy;
use App\Enums\BookStatus;
use App\Services\BookCopyService;
use App\Services\QRCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookCopyController extends Controller
{
    public function __construct(
        protected BookCopyService $bookCopyService
    ) {}

    /**
     * Display a listing of book copies.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'book_id', 'status', 'condition', 'sort_by', 'sort_dir', 'per_page']);

        $copies = $this->bookCopyService->getPaginated($filters);
        $books = Book::orderBy('title')->get();
        $statuses = BookStatus::cases();
        $stats = $this->bookCopyService->getStats();

        return view('book-copies.index', compact('copies', 'books', 'statuses', 'stats'));
    }

    /**
     * Show the form for creating a new book copy.
     */
    public function create(Request $request): View
    {
        \Illuminate\Support\Facades\Gate::authorize('create', BookCopy::class);

        $books = Book::orderBy('title')->get();
        $statuses = BookStatus::cases();
        $selectedBookId = $request->query('book_id');

        return view('book-copies.create', compact('books', 'statuses', 'selectedBookId'));
    }

    /**
     * Store a newly created book copy (supports single and bulk).
     */
    public function store(BookCopyRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $book = Book::findOrFail($data['book_id']);

            if ($request->has('bulk_count') && (int)$request->input('bulk_count') > 0) {
                $count = (int)$request->input('bulk_count');
                $this->bookCopyService->bulkGenerate($book, $count, $data);

                return redirect()
                    ->route('books.show', $book->id)
                    ->with('success', "{$count} eksemplar berhasil dibuat secara massal.");
            }

            $copy = $this->bookCopyService->create($data);

            return redirect()
                ->route('book-copies.show', $copy->id)
                ->with('success', 'Eksemplar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified book copy.
     */
    public function show(string $id): View
    {
        $copy = BookCopy::with('book.category')->findOrFail($id);

        return view('book-copies.show', compact('copy'));
    }

    /**
     * Show the form for editing the specified book copy.
     */
    public function edit(string $id): View
    {
        $copy = BookCopy::findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('update', $copy);

        $books = Book::orderBy('title')->get();
        $statuses = BookStatus::cases();

        return view('book-copies.edit', compact('copy', 'books', 'statuses'));
    }

    /**
     * Update the specified book copy.
     */
    public function update(BookCopyRequest $request, string $id): RedirectResponse
    {
        try {
            $copy = BookCopy::findOrFail($id);
            $data = $request->validated();

            $this->bookCopyService->update($copy, $data);

            return redirect()
                ->route('book-copies.show', $copy->id)
                ->with('success', 'Data eksemplar berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified book copy.
     */
    public function destroy(string $id): RedirectResponse
    {
        $copy = BookCopy::findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('delete', $copy);

        try {
            $bookId = $copy->book_id;
            $this->bookCopyService->delete($copy);

            return redirect()
                ->route('books.show', $bookId)
                ->with('success', 'Eksemplar berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the view for bulk generating book copies.
     */
    public function generate(Request $request): View
    {
        \Illuminate\Support\Facades\Gate::authorize('create', BookCopy::class);

        $books = Book::orderBy('title')->get();
        $selectedBookId = $request->query('book_id');

        return view('book-copies.generate', compact('books', 'selectedBookId'));
    }

    /**
     * Print barcode/QR labels for the copy.
     */
    public function printLabel(string $id, QRCodeService $qrCodeService): View
    {
        $copy = BookCopy::with('book')->findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('update', $copy);
        $qrPayload = $qrCodeService->encode($qrCodeService->bookCopyPayload($copy));

        return view('book-copies.print-label', compact('copy', 'qrPayload'));
    }
}
