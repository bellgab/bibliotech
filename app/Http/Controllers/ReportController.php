<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\BookBorrowing;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index()
    {
        $stats = [
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'active_borrows' => BookBorrowing::whereNull('returned_at')->count(),
            'overdue_books' => BookBorrowing::whereNull('returned_at')
                                          ->where('due_date', '<', Carbon::now())
                                          ->count(),
        ];

        return view('reports.index', compact('stats'));
    }

    /**
     * Generate books report.
     */
    public function books()
    {
        $books = Book::with(['author', 'category'])
                    ->withCount('borrows')
                    ->orderBy('borrows_count', 'desc')
                    ->get();

        $mostPopular = $books->take(10);
        $leastPopular = $books->sortBy('borrows_count')->take(10);

        return view('reports.books', compact('books', 'mostPopular', 'leastPopular'));
    }

    /**
     * Generate users report.
     */
    public function users()
    {
        $users = User::withCount(['borrowedBooks', 'currentlyBorrowedBooks'])
                    ->orderBy('borrowed_books_count', 'desc')
                    ->get();

        $activeUsers = $users->where('is_active', true);
        $inactiveUsers = $users->where('is_active', false);

        return view('reports.users', compact('users', 'activeUsers', 'inactiveUsers'));
    }

    /**
     * Generate borrows report.
     */
    public function borrows()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $monthlyBorrows = BookBorrowing::where('borrowed_at', '>=', $currentMonth)->count();
        $lastMonthBorrows = BookBorrowing::whereBetween('borrowed_at', [$lastMonth, $currentMonth])->count();

        $activeBorrows = BookBorrowing::with(['book.author', 'user'])
                                    ->whereNull('returned_at')
                                    ->orderBy('due_date', 'asc')
                                    ->get();

        $overdueBorrows = BookBorrowing::with(['book.author', 'user'])
                                     ->whereNull('returned_at')
                                     ->where('due_date', '<', Carbon::now())
                                     ->orderBy('due_date', 'asc')
                                     ->get();

        return view('reports.borrows', compact(
            'monthlyBorrows', 
            'lastMonthBorrows', 
            'activeBorrows', 
            'overdueBorrows'
        ));
    }

    /**
     * Generate fines report.
     */
    public function fines()
    {
        $finesData = BookBorrowing::whereNotNull('fine_amount')
                                ->where('fine_amount', '>', 0)
                                ->with(['book.author', 'user'])
                                ->orderBy('returned_at', 'desc')
                                ->get();

        $totalFines = $finesData->sum('fine_amount');
        $unpaidFines = BookBorrowing::whereNull('returned_at')
                                  ->where('due_date', '<', Carbon::now())
                                  ->get()
                                  ->sum(function($borrow) {
                                      return $borrow->calculateFine();
                                  });

        return view('reports.fines', compact('finesData', 'totalFines', 'unpaidFines'));
    }
}
