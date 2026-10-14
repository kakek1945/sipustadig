<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Services\FineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function __construct(
        protected FineService $fineService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Fine::class);

        $fines = $this->fineService->getPaginated([
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'sort_by' => $request->string('sort_by')->toString(),
            'sort_dir' => $request->string('sort_dir')->toString(),
            'per_page' => $request->integer('per_page') ?: null,
        ]);
        $stats = $this->fineService->stats();

        return view('fines.index', compact('fines', 'stats'));
    }

    public function show(string $id): View
    {
        $fine = Fine::with(['member', 'loan', 'loanItem.bookCopy.book', 'processedBy'])->findOrFail($id);
        $this->authorize('view', $fine);

        return view('fines.show', compact('fine'));
    }

    public function pay(Request $request, string $id): RedirectResponse
    {
        $fine = Fine::findOrFail($id);
        $this->authorize('pay', $fine);

        $data = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->fineService->pay($fine, $data['amount'] ?? null, $request->user(), $data['notes'] ?? null);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('fines.show', $fine->id)->with('success', 'Denda berhasil dibayar.');
    }

    public function waive(Request $request, string $id): RedirectResponse
    {
        $fine = Fine::findOrFail($id);
        $this->authorize('waive', $fine);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $this->fineService->waive($fine, $data['reason'], $request->user());
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('fines.show', $fine->id)->with('success', 'Denda berhasil dibebaskan.');
    }
}
