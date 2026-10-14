<?php

namespace App\Http\Controllers;

use App\Services\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function __construct(
        protected ImportService $importService
    ) {}

    public function index(): View
    {
        abort_unless(request()->user()->hasPermission('import_data'), 403);

        $templates = $this->importService->templates();

        return view('imports.index', compact('templates'));
    }

    public function template(string $type)
    {
        abort_unless(request()->user()->hasPermission('import_data'), 403);

        return $this->importService->templateResponse($type);
    }

    public function process(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('import_data'), 403);

        $data = $request->validate([
            'type' => ['required', 'in:members,books,copies'],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
            'dry_run' => ['nullable', 'boolean'],
        ]);

        $result = $this->importService->import($data['type'], $data['file'], $request->boolean('dry_run'));

        if ($result['errors']) {
            return back()
                ->withInput()
                ->with('import_result', $result)
                ->withErrors(['file' => 'Import memiliki error. Periksa laporan baris di bawah.']);
        }

        return back()
            ->with('success', $request->boolean('dry_run') ? 'Preview import valid. Tidak ada data yang disimpan.' : 'Import berhasil diproses.')
            ->with('import_result', $result);
    }
}
