<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService
    ) {}

    public function index(): View
    {
        abort_unless(request()->user()->hasPermission('manage_settings'), 403);

        $settings = collect($this->settingsService->all())
            ->map(fn (array $setting) => (object) $setting)
            ->all();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('manage_settings'), 403);

        $data = $request->validate([
            'loan.max_borrow_days' => ['required', 'numeric', 'min:1', 'max:60'],
            'loan.max_books' => ['required', 'numeric', 'min:1', 'max:20'],
            'loan.max_extensions' => ['required', 'numeric', 'min:0', 'max:10'],
            'loan.max_overdue_days' => ['required', 'numeric', 'min:1', 'max:365'],
            'fine.daily_rate' => ['required', 'numeric', 'min:0'],
            'fine.max_amount' => ['required', 'numeric', 'min:0'],
            'fine.waiver_requires_reason' => ['nullable', 'boolean'],
            'member.max_unpaid_fines' => ['required', 'numeric', 'min:0'],
            'member.card_validity_years' => ['required', 'numeric', 'min:1', 'max:10'],
            'library.name' => ['required', 'string', 'max:255'],
            'library.school_name' => ['nullable', 'string', 'max:255'],
            'library.npsn' => ['nullable', 'string', 'max:50'],
            'library.address' => ['nullable', 'string', 'max:1000'],
            'library.phone' => ['nullable', 'string', 'max:50'],
            'library.email' => ['nullable', 'email', 'max:255'],
            'library.service_hours' => ['nullable', 'string', 'max:255'],
            'library.vision' => ['nullable', 'string', 'max:2000'],
            'library.mission' => ['nullable', 'string', 'max:3000'],
            'library.goals' => ['nullable', 'string', 'max:3000'],
            'library.sop_summary' => ['nullable', 'string', 'max:3000'],
            'library.sop_flow' => ['nullable', 'string', 'max:3000'],
            'library.sop_document_url' => ['nullable', 'url', 'max:1000'],
            'library.principal_name' => ['nullable', 'string', 'max:255'],
            'library.principal_title' => ['nullable', 'string', 'max:255'],
            'library.principal_speech' => ['nullable', 'string', 'max:3000'],
            'library.principal_photo' => ['nullable', 'image', 'max:2048'],
            'system.auto_assign_barcode' => ['nullable', 'boolean'],
            'system.auto_assign_qrcode' => ['nullable', 'boolean'],
        ]);

        $flatData = [
            'loan.max_borrow_days' => $data['loan']['max_borrow_days'],
            'loan.max_books' => $data['loan']['max_books'],
            'loan.max_extensions' => $data['loan']['max_extensions'],
            'loan.max_overdue_days' => $data['loan']['max_overdue_days'],
            'fine.daily_rate' => $data['fine']['daily_rate'],
            'fine.max_amount' => $data['fine']['max_amount'],
            'fine.waiver_requires_reason' => $request->boolean('fine.waiver_requires_reason'),
            'member.max_unpaid_fines' => $data['member']['max_unpaid_fines'],
            'member.card_validity_years' => $data['member']['card_validity_years'],
            'library.name' => $data['library']['name'],
            'library.school_name' => $data['library']['school_name'] ?? '',
            'library.npsn' => $data['library']['npsn'] ?? '',
            'library.address' => $data['library']['address'] ?? '',
            'library.phone' => $data['library']['phone'] ?? '',
            'library.email' => $data['library']['email'] ?? '',
            'library.service_hours' => $data['library']['service_hours'] ?? '',
            'library.vision' => $data['library']['vision'] ?? '',
            'library.mission' => $data['library']['mission'] ?? '',
            'library.goals' => $data['library']['goals'] ?? '',
            'library.sop_summary' => $data['library']['sop_summary'] ?? '',
            'library.sop_flow' => $data['library']['sop_flow'] ?? '',
            'library.sop_document_url' => $data['library']['sop_document_url'] ?? '',
            'library.principal_name' => $data['library']['principal_name'] ?? '',
            'library.principal_title' => $data['library']['principal_title'] ?? '',
            'library.principal_speech' => $data['library']['principal_speech'] ?? '',
            'library.principal_photo' => $this->settingsService->get('library.principal_photo', ''),
            'system.auto_assign_barcode' => $request->boolean('system.auto_assign_barcode'),
            'system.auto_assign_qrcode' => $request->boolean('system.auto_assign_qrcode'),
        ];

        if ($request->hasFile('library.principal_photo')) {
            $oldPhoto = $this->settingsService->get('library.principal_photo', '');
            $disk = config('filesystems.uploads');
            if ($oldPhoto && Storage::disk($disk)->exists($oldPhoto)) {
                Storage::disk($disk)->delete($oldPhoto);
            }

            $flatData['library.principal_photo'] = $request->file('library.principal_photo')->store('library/principal', $disk);
        }

        foreach ($flatData as $key => $value) {
            $this->settingsService->set($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
