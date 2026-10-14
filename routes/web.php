<?php

use Illuminate\Support\Facades\Route;

// Public routes (catalog)
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/catalog', [App\Http\Controllers\CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{id}', [App\Http\Controllers\CatalogController::class, 'show'])->name('catalog.show');

// Auth routes (placeholder)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
})->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => $request->password,
    ]);

    \Illuminate\Support\Facades\Auth::login($user);

    return redirect(route('dashboard'));
})->name('register.post');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dashboard routes (with auth middleware)
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Scan resolver
    Route::get('/scan', [App\Http\Controllers\ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/resolve', [App\Http\Controllers\ScanController::class, 'resolve'])->name('scan.resolve');

    // Members
    Route::get('/members', [App\Http\Controllers\MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [App\Http\Controllers\MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [App\Http\Controllers\MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}', [App\Http\Controllers\MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/print-card', [App\Http\Controllers\MemberController::class, 'printCard'])->name('members.print-card');
    Route::get('/members/{member}/edit', [App\Http\Controllers\MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{member}', [App\Http\Controllers\MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [App\Http\Controllers\MemberController::class, 'destroy'])->name('members.destroy');
    Route::post('/members/{member}/block', [App\Http\Controllers\MemberController::class, 'block'])->name('members.block');
    Route::post('/members/{member}/unblock', [App\Http\Controllers\MemberController::class, 'unblock'])->name('members.unblock');

    // Categories
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Books
    Route::get('/books', [App\Http\Controllers\BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [App\Http\Controllers\BookController::class, 'create'])->name('books.create');
    Route::post('/books', [App\Http\Controllers\BookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}', [App\Http\Controllers\BookController::class, 'show'])->name('books.show');
    Route::get('/books/{id}/edit', [App\Http\Controllers\BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [App\Http\Controllers\BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [App\Http\Controllers\BookController::class, 'destroy'])->name('books.destroy');

    // Book Copies
    Route::get('/book-copies', [App\Http\Controllers\BookCopyController::class, 'index'])->name('book-copies.index');
    Route::get('/book-copies/create', [App\Http\Controllers\BookCopyController::class, 'create'])->name('book-copies.create');
    Route::get('/book-copies/generate', [App\Http\Controllers\BookCopyController::class, 'generate'])->name('book-copies.generate');
    Route::post('/book-copies', [App\Http\Controllers\BookCopyController::class, 'store'])->name('book-copies.store');
    Route::get('/book-copies/{id}', [App\Http\Controllers\BookCopyController::class, 'show'])->name('book-copies.show');
    Route::get('/book-copies/{id}/edit', [App\Http\Controllers\BookCopyController::class, 'edit'])->name('book-copies.edit');
    Route::put('/book-copies/{id}', [App\Http\Controllers\BookCopyController::class, 'update'])->name('book-copies.update');
    Route::delete('/book-copies/{id}', [App\Http\Controllers\BookCopyController::class, 'destroy'])->name('book-copies.destroy');
    Route::get('/book-copies/{id}/print-label', [App\Http\Controllers\BookCopyController::class, 'printLabel'])->name('book-copies.print-label');

    // Loans
    Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [App\Http\Controllers\LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{id}', [App\Http\Controllers\LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{id}/extend', [App\Http\Controllers\LoanController::class, 'extend'])->name('loans.extend');

    // Returns
    Route::get('/returns', [App\Http\Controllers\ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/lookup', [App\Http\Controllers\ReturnController::class, 'lookup'])->name('returns.lookup');
    Route::post('/returns', [App\Http\Controllers\ReturnController::class, 'process'])->name('returns.process');

    // Fines
    Route::get('/fines', [App\Http\Controllers\FineController::class, 'index'])->name('fines.index');
    Route::get('/fines/{id}', [App\Http\Controllers\FineController::class, 'show'])->name('fines.show');
    Route::post('/fines/{id}/pay', [App\Http\Controllers\FineController::class, 'pay'])->name('fines.pay');
    Route::post('/fines/{id}/waive', [App\Http\Controllers\FineController::class, 'waive'])->name('fines.waive');

    // Reports
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/{type}', [App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');

    // Settings
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // Audit Logs
    Route::get('/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{id}', [App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');

    // Import
    Route::get('/import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
    Route::get('/import/template/{type}', [App\Http\Controllers\ImportController::class, 'template'])->name('import.template');
    Route::post('/import', [App\Http\Controllers\ImportController::class, 'process'])->name('import.process');
});
