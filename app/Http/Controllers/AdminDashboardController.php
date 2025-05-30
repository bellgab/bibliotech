<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\BookBorrowing;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Admin dashboard with advanced statistics
     */
    public function index()
    {
        // Basic statistics
        $stats = [
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'active_borrows' => BookBorrowing::whereNull('returned_at')->count(),
            'overdue_books' => BookBorrowing::whereNull('returned_at')
                                           ->where('due_date', '<', Carbon::now())
                                           ->count(),
        ];

        // Trending statistics
        $trending = [
            'books_added_this_month' => Book::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'new_users_this_month' => User::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'borrows_this_month' => BookBorrowing::where('borrowed_at', '>=', Carbon::now()->startOfMonth())->count(),
            'returns_this_month' => BookBorrowing::whereNotNull('returned_at')
                                                 ->where('returned_at', '>=', Carbon::now()->startOfMonth())
                                                 ->count(),
        ];

        // Popular books (most borrowed)
        $popularBooks = Book::withCount(['borrows'])
                           ->orderBy('borrows_count', 'desc')
                           ->limit(5)
                           ->get();

        // Active users (most borrows)
        $activeUsers = User::withCount(['borrowedBooks'])
                          ->orderBy('borrowed_books_count', 'desc')
                          ->limit(5)
                          ->get();

        // Notification statistics
        $notificationStats = $this->notificationService->getNotificationStats();

        // Recent activities
        $recentBorrows = BookBorrowing::with(['book', 'user'])
                                     ->latest('borrowed_at')
                                     ->limit(10)
                                     ->get();

        $recentReturns = BookBorrowing::with(['book', 'user'])
                                     ->whereNotNull('returned_at')
                                     ->latest('returned_at')
                                     ->limit(10)
                                     ->get();

        // System health indicators
        $systemHealth = [
            'database_connection' => $this->checkDatabaseConnection(),
            'storage_writable' => is_writable(storage_path()),
            'cache_working' => $this->checkCacheWorking(),
            'mail_configured' => config('mail.default') !== null,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'trending',
            'popularBooks',
            'activeUsers',
            'notificationStats',
            'recentBorrows',
            'recentReturns',
            'systemHealth'
        ));
    }

    /**
     * Analytics page with detailed charts and data
     */
    public function analytics()
    {
        // Monthly borrow statistics for chart
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('M Y'),
                'borrows' => BookBorrowing::whereYear('borrowed_at', $date->year)
                                         ->whereMonth('borrowed_at', $date->month)
                                         ->count(),
                'returns' => BookBorrowing::whereYear('returned_at', $date->year)
                                         ->whereMonth('returned_at', $date->month)
                                         ->count(),
            ];
        }

        // Category statistics
        $categoryStats = Book::selectRaw('categories.name, COUNT(*) as books_count, SUM(books.total_copies) as total_copies')
                            ->join('categories', 'books.category_id', '=', 'categories.id')
                            ->groupBy('categories.id', 'categories.name')
                            ->orderBy('books_count', 'desc')
                            ->get();

        // User activity patterns
        $userActivityStats = User::selectRaw('
                CASE 
                    WHEN (SELECT COUNT(*) FROM book_borrowings WHERE book_borrowings.user_id = users.id) = 0 THEN "Inactive"
                    WHEN (SELECT COUNT(*) FROM book_borrowings WHERE book_borrowings.user_id = users.id) BETWEEN 1 AND 5 THEN "Light Reader"
                    WHEN (SELECT COUNT(*) FROM book_borrowings WHERE book_borrowings.user_id = users.id) BETWEEN 6 AND 15 THEN "Regular Reader"
                    WHEN (SELECT COUNT(*) FROM book_borrowings WHERE book_borrowings.user_id = users.id) > 15 THEN "Heavy Reader"
                END as activity_level,
                COUNT(*) as user_count
            ')
            ->groupBy('activity_level')
            ->get();

        return view('admin.analytics', compact(
            'monthlyStats',
            'categoryStats',
            'userActivityStats'
        ));
    }

    /**
     * Send test notification
     */
    public function testNotification(Request $request)
    {
        try {
            $stats = $this->notificationService->getNotificationStats();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification test completed',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheWorking(): bool
    {
        try {
            \Cache::put('test_key', 'test_value', 60);
            return \Cache::get('test_key') === 'test_value';
        } catch (\Exception $e) {
            return false;
        }
    }
}
