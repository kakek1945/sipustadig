<?php

namespace App\Http\Controllers;

use App\Services\QRCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function __construct(
        protected QRCodeService $qrCodeService
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->canManage(), 403);

        return view('scan.index');
    }

    public function resolve(Request $request): JsonResponse
    {
        abort_unless($request->user()->canManage(), 403);

        $data = $request->validate([
            'scan' => ['required', 'string', 'max:1000'],
        ]);

        $result = $this->qrCodeService->resolve($data['scan']);

        return response()->json($result, $result['found'] ? 200 : 404);
    }
}
