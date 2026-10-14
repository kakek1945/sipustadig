<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Services\MemberService;
use App\Services\QRCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        protected MemberService $memberService
    ) {}

    /**
     * Display a listing of members.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'type', 'class', 'status', 'year_level', 'sort_by', 'sort_dir']);

        $members = $this->memberService->getPaginated($filters);
        $classes = $this->memberService->getUniqueClasses();
        $yearLevels = $this->memberService->getUniqueYearLevels();
        $stats = $this->memberService->getStats();

        return view('members.index', compact('members', 'classes', 'yearLevels', 'stats'));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create(): View
    {
        return view('members.create');
    }

    /**
     * Store a newly created member.
     */
    public function store(MemberRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('photos/members', config('filesystems.uploads'));
            }

            $member = $this->memberService->create($data);

            return redirect()
                ->route('members.show', $member)
                ->with('success', 'Anggota berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member): View
    {
        $member->load(['loans' => function ($query) {
            $query->with(['items.bookCopy.book', 'processedBy'])
                ->orderBy('loan_date', 'desc')
                ->limit(10);
        }, 'fines' => function ($query) {
            $query->orderBy('created_at', 'desc')
                ->limit(10);
        }, 'user']);

        // Calculate stats
        $activeLoansCount = $member->activeLoansCount();
        $overdueLoansCount = $member->overdueLoansCount();
        $unpaidFinesTotal = $member->unpaidFinesTotal();
        $totalLoans = $member->loans()->count();

        return view('members.show', compact(
            'member',
            'activeLoansCount',
            'overdueLoansCount',
            'unpaidFinesTotal',
            'totalLoans'
        ));
    }

    public function printCard(Member $member, QRCodeService $qrCodeService): View
    {
        $qrPayload = $qrCodeService->encode($qrCodeService->memberPayload($member));

        return view('members.print-card', compact('member', 'qrPayload'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified member.
     */
    public function update(MemberRequest $request, Member $member): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                $disk = config('filesystems.uploads');
                if ($member->photo && Storage::disk($disk)->exists($member->photo)) {
                    Storage::disk($disk)->delete($member->photo);
                }
                $data['photo'] = $request->file('photo')->store('photos/members', $disk);
            }

            $this->memberService->update($member, $data);

            return redirect()
                ->route('members.show', $member)
                ->with('success', 'Data anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Member $member): RedirectResponse
    {
        try {
            $this->memberService->delete($member);

            return redirect()
                ->route('members.index')
                ->with('success', 'Anggota berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Block a member.
     */
    public function block(Request $request, Member $member): RedirectResponse
    {
        try {
            $this->memberService->block($member, $request->get('reason'));

            return back()->with('success', 'Anggota berhasil diblokir.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Unblock a member.
     */
    public function unblock(Member $member): RedirectResponse
    {
        try {
            $this->memberService->unblock($member);

            return back()->with('success', 'Anggota berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Export members to CSV/Excel.
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'status']);
        $members = $this->memberService->getAllForExport($filters);

        // Generate CSV
        $csv = [];
        $csv[] = ['No', 'Nama', 'Email', 'NISN/NIP', 'Tipe', 'Kelas', 'Status', 'Tanggal Gabung'];

        foreach ($members as $index => $member) {
            $csv[] = [
                $index + 1,
                $member->name,
                $member->email,
                $member->type === 'student' ? $member->nisn : $member->employee_number,
                $member->type === 'student' ? 'Siswa' : 'Guru',
                $member->class ?? '-',
                $member->status,
                $member->join_date->format('d/m/Y'),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $csv,
        ]);
    }
}