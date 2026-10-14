<?php

namespace App\Http\Controllers;

use App\Models\LoanItem;
use App\Services\ReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __construct(
        protected ReturnService $returnService
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage_returns'), 403);

        $borrowedItems = $this->returnService->getBorrowedItems([
            'search' => request('search'),
            'per_page' => request('per_page'),
        ]);
        $stats = $this->returnService->stats();

        return view('returns.index', compact('borrowedItems', 'stats'));
    }

    public function lookup(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasPermission('manage_returns'), 403);

        $data = $request->validate([
            'book_code' => ['required', 'string'],
        ]);

        $loanItem = $this->returnService->findBorrowedItemByCode($data['book_code']);

        if (!$loanItem) {
            return response()->json(['message' => 'Eksemplar tidak ditemukan atau belum dipinjam.'], 404);
        }

        $lateDays = $this->returnService->calculateLateDays($loanItem);
        $fineAmount = $this->returnService->calculateFineAmount($lateDays);

        return response()->json([
            'id' => $loanItem->id,
            'loan_id' => $loanItem->loan_id,
            'member' => [
                'name' => $loanItem->loan->member->name,
                'member_number' => $loanItem->loan->member->member_number,
            ],
            'book' => [
                'title' => $loanItem->bookCopy->book?->title,
                'author' => $loanItem->bookCopy->book?->author,
            ],
            'book_copy' => [
                'code' => $loanItem->bookCopy->copy_code,
                'barcode' => $loanItem->bookCopy->barcode,
            ],
            'loan_date' => $loanItem->loan->loan_date->format('d M Y'),
            'due_date' => $loanItem->due_date->format('d M Y'),
            'is_overdue' => $lateDays > 0,
            'overdue_days' => $lateDays,
            'fine_amount' => $fineAmount,
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('manage_returns'), 403);

        $data = $request->validate([
            'book_code' => ['required', 'string'],
            'return_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $loanItem = $this->returnService->findBorrowedItemByCode($data['book_code']);

        if (!$loanItem) {
            return back()->withErrors(['book_code' => 'Eksemplar yang dipindai tidak ditemukan atau belum dipinjam.']);
        }

        try {
            $result = $this->returnService->processReturn($loanItem, $request->user(), $data);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        $message = 'Pengembalian berhasil diproses.';
        if (!empty($result['fine'])) {
            $message .= ' Denda Rp ' . number_format($result['fine_amount'], 0, ',', '.') . ' berhasil dibuat.';
        }

        return redirect()
            ->route('returns.index')
            ->with('success', $message);
    }
}
