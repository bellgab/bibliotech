<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\BookBorrowing;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemStatusController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display comprehensive system status
     */
    public function index()
    {
        // System Health Checks
        $systemHealth = [
            'database' => $this->checkDatabaseConnection(),
            'cache' => $this->checkCacheWorking(),
            'storage' => $this->checkStorageWritable(),
            'mail' => $this->checkMailConfiguration(),
            'qr_package' => $this->checkQrPackage(),
            'schedule' => $this->checkScheduleStatus(),
        ];

        // Performance Metrics
        $performance = [
            'database_size' => $this->getDatabaseSize(),
            'cache_size' => $this->getCacheSize(),
            'storage_size' => $this->getStorageSize(),
            'memory_usage' => $this->getMemoryUsage(),
            'average_response_time' => $this->getAverageResponseTime(),
        ];

        // Recent Activity Summary
        $recentActivity = [
            'new_books_today' => Book::whereDate('created_at', Carbon::today())->count(),
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
            'borrows_today' => BookBorrowing::whereDate('borrowed_at', Carbon::today())->count(),
            'returns_today' => BookBorrowing::whereDate('returned_at', Carbon::today())->count(),
            'overdue_books' => BookBorrowing::whereNull('returned_at')
                                          ->where('due_date', '<', Carbon::now())
                                          ->count(),
        ];

        // System Configuration
        $configuration = [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_version' => $this->getAppVersion(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];

        // Feature Status
        $features = [
            'qr_codes' => class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'),
            'email_notifications' => !empty(config('mail.default')),
            'file_uploads' => is_writable(storage_path('app/public')),
            'user_registration' => true, // Always enabled
            'admin_dashboard' => auth()->user() && auth()->user()->role === 'admin',
        ];

        // Error Log Summary (last 24 hours)
        $errorSummary = $this->getErrorSummary();

        return view('admin.system-status', compact(
            'systemHealth',
            'performance',
            'recentActivity',
            'configuration',
            'features',
            'errorSummary'
        ));
    }

    /**
     * Get system diagnostics as JSON for API calls
     */
    public function diagnostics()
    {
        return response()->json([
            'timestamp' => now()->toISOString(),
            'status' => 'healthy',
            'checks' => [
                'database' => $this->checkDatabaseConnection(),
                'cache' => $this->checkCacheWorking(),
                'storage' => $this->checkStorageWritable(),
                'mail' => $this->checkMailConfiguration(),
            ],
            'metrics' => [
                'users_count' => User::count(),
                'books_count' => Book::count(),
                'active_borrows' => BookBorrowing::whereNull('returned_at')->count(),
                'overdue_books' => BookBorrowing::whereNull('returned_at')
                                                ->where('due_date', '<', Carbon::now())
                                                ->count(),
            ]
        ]);
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheWorking(): bool
    {
        try {
            $testKey = 'system_status_test_' . time();
            Cache::put($testKey, 'test_value', 60);
            $result = Cache::get($testKey) === 'test_value';
            Cache::forget($testKey);
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorageWritable(): bool
    {
        return is_writable(storage_path()) && is_writable(storage_path('app'));
    }

    private function checkMailConfiguration(): bool
    {
        return !empty(config('mail.default')) && 
               !empty(config('mail.from.address')) && 
               !empty(config('mail.from.name'));
    }

    private function checkQrPackage(): bool
    {
        return class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode');
    }

    private function checkScheduleStatus(): bool
    {
        // Check if Laravel scheduler is configured (basic check)
        return file_exists(base_path('app/Console/Kernel.php'));
    }

    private function getDatabaseSize(): string
    {
        try {
            $tables = DB::select("SELECT table_name AS 'table', 
                                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                                 FROM information_schema.TABLES 
                                 WHERE table_schema = ?", [config('database.connections.mysql.database')]);
            
            $totalSize = collect($tables)->sum('size_mb');
            return $totalSize . ' MB';
        } catch (\Exception $e) {
            return 'Unable to calculate';
        }
    }

    private function getCacheSize(): string
    {
        try {
            // This is a simplified check - actual implementation depends on cache driver
            return 'N/A';
        } catch (\Exception $e) {
            return 'Unable to calculate';
        }
    }

    private function getStorageSize(): string
    {
        try {
            $size = 0;
            $path = storage_path('app');
            if (is_dir($path)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path)
                );
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                    }
                }
            }
            return round($size / 1024 / 1024, 2) . ' MB';
        } catch (\Exception $e) {
            return 'Unable to calculate';
        }
    }

    private function getMemoryUsage(): string
    {
        return round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';
    }

    private function getAverageResponseTime(): string
    {
        // This would require implementing response time logging
        return 'N/A';
    }

    private function getAppVersion(): string
    {
        try {
            if (file_exists(base_path('composer.json'))) {
                $composer = json_decode(file_get_contents(base_path('composer.json')), true);
                return $composer['version'] ?? '1.0.0';
            }
            return '1.0.0';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getErrorSummary(): array
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) {
                return ['total' => 0, 'recent' => 0];
            }

            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);
            
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $today = Carbon::today()->format('Y-m-d');
            
            $recentErrors = 0;
            $totalErrors = 0;
            
            foreach ($lines as $line) {
                if (str_contains($line, '.ERROR:') || str_contains($line, '.CRITICAL:')) {
                    $totalErrors++;
                    if (str_contains($line, $yesterday) || str_contains($line, $today)) {
                        $recentErrors++;
                    }
                }
            }
            
            return ['total' => $totalErrors, 'recent' => $recentErrors];
        } catch (\Exception $e) {
            return ['total' => 0, 'recent' => 0];
        }
    }
}
