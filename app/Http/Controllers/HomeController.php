<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $settings = app('settings');

        $profile = [
            'library_name' => $settings->get('library.name', 'Perpustakaan Sekolah'),
            'school_name' => $settings->get('library.school_name', 'Sekolah'),
            'npsn' => $settings->get('library.npsn', ''),
            'address' => $settings->get('library.address', ''),
            'phone' => $settings->get('library.phone', ''),
            'email' => $settings->get('library.email', ''),
            'service_hours' => $settings->get('library.service_hours', ''),
            'vision' => $settings->get('library.vision', ''),
            'mission' => $settings->get('library.mission', ''),
            'goals' => $settings->get('library.goals', ''),
            'sop_summary' => $settings->get('library.sop_summary', ''),
            'sop_flow' => $settings->get('library.sop_flow', ''),
            'sop_document_url' => $settings->get('library.sop_document_url', ''),
            'principal_name' => $settings->get('library.principal_name', ''),
            'principal_title' => $settings->get('library.principal_title', 'Kepala Sekolah'),
            'principal_speech' => $settings->get('library.principal_speech', ''),
            'principal_photo' => $settings->get('library.principal_photo', ''),
        ];

        $stats = [
            'total_titles' => Book::count(),
            'available_copies' => BookCopy::where('status', BookStatus::Available->value)->count(),
            'categories' => Category::count(),
            'new_books' => Book::whereDate('created_at', '>=', now()->subDays(30)->toDateString())->count(),
        ];

        $newBooks = Book::query()
            ->with('category')
            ->latest()
            ->limit(4)
            ->get();

        return view('welcome', compact('profile', 'stats', 'newBooks'));
    }
}
