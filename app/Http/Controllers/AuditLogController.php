<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('view_audit_logs'), 403);

        $logs = $this->auditService->getPaginated($request->only(['search', 'action', 'model_type', 'date', 'per_page']));
        $actions = $this->auditService->actions();
        $modelTypes = $this->auditService->modelTypes();

        return view('audit-logs.index', compact('logs', 'actions', 'modelTypes'));
    }

    public function show(string $id): View
    {
        abort_unless(request()->user()->hasPermission('view_audit_logs'), 403);

        $log = AuditLog::with('user')->findOrFail($id);

        return view('audit-logs.show', compact('log'));
    }
}
