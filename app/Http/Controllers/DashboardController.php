<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $stats = $this->dashboardService->stats();
        $recent_loans = $this->dashboardService->recentLoans();
        $popular_books = $this->dashboardService->popularBooks();
        $loan_trend = $this->dashboardService->loanTrend();
        $category_distribution = $this->dashboardService->categoryDistribution();
        $principal_summary = $this->dashboardService->principalSummary();
        $role = Auth::user()?->role;

        return view('dashboard.index', compact(
            'stats',
            'recent_loans',
            'popular_books',
            'loan_trend',
            'category_distribution',
            'principal_summary',
            'role'
        ));
    }
}
