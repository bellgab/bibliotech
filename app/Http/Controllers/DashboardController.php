<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\BookBorrowing;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // If user is not authenticated, show public landing page
        if (!auth()->check()) {
            return view('dashboard');
        }

        // Get statistics for authenticated users
        $totalBooks = Book::count();
        $availableBooks = Book::where('available_copies', '>', 0)->count();
        $borrowedBooks = BookBorrowing::whereNull('returned_at')->count();
        $overdueBooks = BookBorrowing::whereNull('returned_at')
            ->where('due_date', '<', Carbon::now())
            ->count();

        // Get recent borrows (last 10)
        $recentBorrows = BookBorrowing::with(['book', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get popular books (books with most borrows)
        $popularBooks = Book::with('author')
            ->withCount('borrows')
            ->orderBy('borrows_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'availableBooks', 
            'borrowedBooks',
            'overdueBooks',
            'recentBorrows',
            'popularBooks'
        ));
    }
}
