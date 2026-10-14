<?php

namespace App\Http\Controllers;

use App\Actions\CreateLoan;
use App\Actions\ExtendLoan;
use App\Http\Requests\LoanRequest;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Member;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $loanService,
        protected CreateLoan $createLoan,
        protected ExtendLoan $extendLoan
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Loan::class);

        $filters = $request->only(['search', 'tab', 'member_id', 'sort_by', 'sort_dir', 'per_page']);
        $loans = $this->loanService->getPaginated($filters);
        $stats = $this->loanService->stats();

        return view('loans.index', compact('loans', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('create', Loan::class);

        $members = Member::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $bookCopies = BookCopy::query()
            ->with('book')
            ->where('status', 'available')
            ->orderBy('copy_code')
            ->get();

        $loanDuration = (int) app('settings')->get('loan.max_borrow_days', 7);
        $maxBooks = (int) app('settings')->get('loan.max_books', 3);

        return view('loans.create', compact('members', 'bookCopies', 'loanDuration', 'maxBooks'));
    }

    public function store(LoanRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $member = Member::findOrFail($data['member_id']);

            $loan = $this->createLoan->handle(
                $member,
                $data['book_copy_ids'],
                $request->user(),
                $data
            );

            return redirect()
                ->route('loans.show', $loan->id)
                ->with('success', 'Peminjaman berhasil dicatat.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(string $id): View
    {
        $loan = Loan::with(['member', 'processedBy', 'items.bookCopy.book'])->findOrFail($id);
        Gate::authorize('view', $loan);

        return view('loans.show', compact('loan'));
    }

    public function extend(Request $request, string $id): RedirectResponse
    {
        $loan = Loan::findOrFail($id);
        Gate::authorize('extend', $loan);

        $data = $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->extendLoan->handle($loan, $request->user(), $data['days'] ?? null, $data['notes'] ?? null);

        return redirect()
            ->route('loans.show', $loan->id)
            ->with('success', 'Peminjaman berhasil diperpanjang.');
    }
}
