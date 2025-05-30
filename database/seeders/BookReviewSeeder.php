<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookReview;
use App\Models\User;
use App\Models\Book;

class BookReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and books
        $users = User::where('is_admin', false)->take(10)->get();
        $books = Book::take(15)->get();

        if ($users->isEmpty() || $books->isEmpty()) {
            $this->command->info('Nincs elegendő felhasználó vagy könyv az értékelések generálásához.');
            return;
        }

        $comments = [
            'Fantasztikus könyv! Nagyon élveztem az olvasását.',
            'Érdekes történet, de kissé lassú a kezdet.',
            'Kiváló mű, mindenkinek ajánlom!',
            'Jó könyv, de voltak unalmas részek is.',
            'Lenyűgöző írás, nem tudtam letenni!',
            'Átlagos könyv, semmi különleges.',
            'Gyönyörű stílusban írt mű, nagy élmény volt.',
            'Nagyon tanulságos könyv, sokat gondolkodtam rajta.',
            'Szórakoztató olvasmány, könnyű stílus.',
            'Mélyen megindító történet.',
            'Izgalmas cselekmény, fordulatos események.',
            'Remek karakterfejlődés és történetvezetés.',
            'Kissé hosszadalmas, de megérte végigolvasni.',
            'Lenyűgöző világépítés és atmoszféra.',
            'Egyszerű, de hatásos történet.'
        ];

        $reviewsCreated = 0;

        foreach ($books as $book) {
            // Random number of reviews per book (0-5)
            $reviewCount = rand(0, 5);
            
            // Get random users for this book
            $randomUsers = $users->random(min($reviewCount, $users->count()));
            
            foreach ($randomUsers as $user) {
                // Check if user already reviewed this book
                $existingReview = BookReview::where('user_id', $user->id)
                                          ->where('book_id', $book->id)
                                          ->first();
                
                if ($existingReview) {
                    continue;
                }

                $rating = rand(1, 5);
                $comment = $rating >= 4 ? 
                    $comments[array_rand(array_slice($comments, 0, 10))] : 
                    $comments[array_rand(array_slice($comments, 5, 10))];

                // 80% chance to be approved, 20% pending
                $isApproved = rand(1, 100) <= 80;
                $approvedAt = $isApproved ? now()->subDays(rand(1, 30)) : null;
                $approvedBy = $isApproved ? User::where('is_admin', true)->first()?->id : null;

                BookReview::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_approved' => $isApproved,
                    'approved_at' => $approvedAt,
                    'approved_by' => $approvedBy,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);

                $reviewsCreated++;
            }
        }

        $this->command->info("$reviewsCreated értékelés sikeresen létrehozva!");
    }
}
