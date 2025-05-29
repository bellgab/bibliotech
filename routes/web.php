<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ReportController;

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

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Books
    Route::resource('books', BookController::class);
    
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
    });
    
    // Profile routes
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});
