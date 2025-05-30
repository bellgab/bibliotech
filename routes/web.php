<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SystemStatusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function() {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Debug routes for CSRF testing
Route::get('/debug/csrf', function (Illuminate\Http\Request $request) {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'session_cookie' => config('session.cookie'),
        'app_key_exists' => !empty(config('app.key')),
        'session_lifetime' => config('session.lifetime'),
        'secure_cookie' => config('session.secure'),
        'headers' => $request->headers->all(),
    ]);
})->middleware('web');

Route::get('/debug/csrf-form', function () {
    return view('debug.csrf-test');
})->middleware('web')->name('debug.csrf.form');

Route::post('/debug/csrf-test', function (Illuminate\Http\Request $request) {
    $request->validate([
        'test_field' => 'required|string|max:255'
    ]);
    
    return redirect()->route('debug.csrf.form')->with('success', 'CSRF verification passed! Test field: ' . $request->test_field);
})->middleware('web')->name('debug.csrf.post');

// CSRF Diagnostics Route
Route::get('/debug/csrf-diagnostics', function (Illuminate\Http\Request $request) {
    $sessionData = session()->all();
    
    return response()->json([
        'environment' => [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_key_exists' => !empty(config('app.key')),
            'app_url' => config('app.url'),
        ],
        'session' => [
            'driver' => config('session.driver'),
            'lifetime' => config('session.lifetime'),
            'expire_on_close' => config('session.expire_on_close'),
            'encrypt' => config('session.encrypt'),
            'cookie' => config('session.cookie'),
            'path' => config('session.path'),
            'domain' => config('session.domain'),
            'secure' => config('session.secure'),
            'http_only' => config('session.http_only'),
            'same_site' => config('session.same_site'),
        ],
        'request' => [
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'has_session' => $request->hasSession(),
        ],
        'current_session' => [
            'id' => session()->getId(),
            'token' => session()->token(),
            'data_count' => count($sessionData),
            'regenerated' => session()->isStarted(),
        ],
        'middleware' => [
            'csrf_middleware_loaded' => class_exists('App\Http\Middleware\VerifyCsrfToken'),
            'session_middleware_loaded' => class_exists('Illuminate\Session\Middleware\StartSession'),
        ],
        'storage' => [
            'session_path' => storage_path('framework/sessions'),
            'session_path_writable' => is_writable(storage_path('framework/sessions')),
            'session_files_count' => count(glob(storage_path('framework/sessions/*'))),
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware('web')->name('debug.csrf.diagnostics');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Books
    Route::resource('books', BookController::class);
    
    // Book Reviews
    Route::resource('reviews', ReviewController::class);
    Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    
    // Borrows
    Route::resource('borrows', BorrowController::class);
    Route::post('/borrows/{borrow}/return', [BorrowController::class, 'returnBook'])->name('borrows.return');
    
    // Admin and Librarian only routes
    Route::middleware(['role:admin,librarian'])->group(function () {
        // Users
        Route::resource('users', UserController::class);
        
        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/books', [ReportController::class, 'books'])->name('reports.books');
        Route::get('/reports/users', [ReportController::class, 'users'])->name('reports.users');
        Route::get('/reports/borrows', [ReportController::class, 'borrows'])->name('reports.borrows');
        Route::get('/reports/fines', [ReportController::class, 'fines'])->name('reports.fines');
        
        // Admin Dashboard
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/analytics', [AdminDashboardController::class, 'analytics'])->name('admin.analytics');
        Route::post('/admin/test-notification', [AdminDashboardController::class, 'testNotification'])->name('admin.test-notification');
        
        // System Status (Admin only)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin/system-status', [SystemStatusController::class, 'index'])->name('admin.system-status');
            Route::get('/admin/system-diagnostics', [SystemStatusController::class, 'diagnostics'])->name('system.diagnostics');
        });
    });
    
    // Profile routes
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // QR Code routes
    Route::get('/qrcodes', [QrCodeController::class, 'index'])->name('qrcodes.index');
    Route::get('/qrcodes/generate', [QrCodeController::class, 'generate'])->name('qrcodes.generate');
    Route::get('/books/{book}/qr', [QrCodeController::class, 'generateBookQr'])->name('books.qr');
    Route::post('/qr/quick-borrow', [QrCodeController::class, 'quickBorrow'])->name('qr.quick-borrow');
});

// QR Code public access (for mobile scanning)
Route::get('/qr/book/{book}', [QrCodeController::class, 'showBookInfo'])->name('qr.book.info');
