<?php

// Egyszerű teszt script a könyvértékelési rendszer ellenőrzésére
// Futtasd: php test_reviews.php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookReview;
use App\Models\Book;
use App\Models\User;

echo "=== Könyvértékelési Rendszer Teszt ===\n\n";

// Statisztikák
$totalReviews = BookReview::count();
$approvedReviews = BookReview::where('is_approved', true)->count();
$pendingReviews = BookReview::where('is_approved', false)->count();
$averageRating = BookReview::where('is_approved', true)->avg('rating');

echo "📊 Statisztikák:\n";
echo "- Összes értékelés: $totalReviews\n";
echo "- Jóváhagyott: $approvedReviews\n";
echo "- Jóváhagyásra vár: $pendingReviews\n";
echo "- Átlagos értékelés: " . round($averageRating, 2) . "/5\n\n";

// Legjobban értékelt könyvek
echo "🏆 Top 5 legjobban értékelt könyv:\n";
$topBooks = Book::withAvg(['reviews' => function($query) {
        $query->where('is_approved', true);
    }], 'rating')
    ->withCount(['reviews' => function($query) {
        $query->where('is_approved', true);
    }])
    ->having('reviews_count', '>=', 1)
    ->orderBy('reviews_avg_rating', 'desc')
    ->limit(5)
    ->get();

foreach ($topBooks as $book) {
    echo "- {$book->title}: " . round($book->reviews_avg_rating, 1) . "/5 ({$book->reviews_count} értékelés)\n";
}

echo "\n📋 Jóváhagyásra váró értékelések:\n";
$pendingReviewsList = BookReview::with(['book', 'user'])
    ->where('is_approved', false)
    ->latest()
    ->take(3)
    ->get();

if ($pendingReviewsList->count() > 0) {
    foreach ($pendingReviewsList as $review) {
        echo "- {$review->book->title} ({$review->rating}/5) - {$review->user->name}\n";
    }
} else {
    echo "- Nincs jóváhagyásra váró értékelés\n";
}

echo "\n✅ Teszt befejezve!\n";
