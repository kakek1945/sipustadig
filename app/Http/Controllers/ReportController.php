<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected ExportService $exportService
    ) {}

    public function index(): View
    {
        abort_unless(request()->user()->hasPermission('view_reports'), 403);

        $reportTypes = $this->reportService->types();

        return view('reports.index', compact('reportTypes'));
    }

    public function show(Request $request, string $type): View
    {
        abort_unless($request->user()->hasPermission('view_reports'), 403);

        $reportTypes = $this->reportService->types();
        $report = $this->reportService->get($type, $request->only(['start_date', 'end_date', 'year']));

        return view('reports.show', compact('type', 'reportTypes', 'report'));
    }

    public function export(Request $request)
    {
        abort_unless($request->user()->hasPermission('view_reports'), 403);

        $type = $request->get('type');
        $format = $request->get('format', 'csv');
        $report = $this->reportService->get($type, $request->only(['start_date', 'end_date', 'year']));

        if (!in_array($format, ['csv', 'excel'], true)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Format export tidak didukung.');
        }

        return $this->exportService->csv($type . '-' . now()->format('Ymd-His') . '.csv', $report['columns'], $report['rows']);
    }
}
