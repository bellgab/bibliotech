<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Irodalom', 'description' => 'Magyar és világirodalom'],
            ['name' => 'Informatika', 'description' => 'Programozás, technológia'],
            ['name' => 'Történelem', 'description' => 'Történelmi könyvek'],
            ['name' => 'Tudomány', 'description' => 'Természettudományok'],
            ['name' => 'Művészet', 'description' => 'Képzőművészet, zene, színház'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create authors
        $authors = [
            ['name' => 'Arany János', 'biography' => 'Magyar költő, író'],
            ['name' => 'Petőfi Sándor', 'biography' => 'Magyar költő, forradalmár'],
            ['name' => 'J.K. Rowling', 'biography' => 'Brit író, a Harry Potter sorozat szerzője'],
            ['name' => 'Isaac Asimov', 'biography' => 'Orosz-amerikai sci-fi író'],
            ['name' => 'Agatha Christie', 'biography' => 'Brit krimiíró'],
        ];

        foreach ($authors as $authorData) {
            Author::create($authorData);
        }

        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@bibliotech.hu',
            'password' => Hash::make('password'),
            'phone' => '+36301234567',
            'address' => 'Budapest, Kossuth Lajos u. 1.',
            'membership_number' => 'ADM001',
            'membership_type' => 'admin',
            'is_active' => true,
        ]);

        // Create librarian user
        User::create([
            'name' => 'Könyvtáros',
            'email' => 'librarian@bibliotech.hu',
            'password' => Hash::make('password'),
            'phone' => '+36301234568',
            'address' => 'Budapest, Váci u. 15.',
            'membership_number' => 'LIB001',
            'membership_type' => 'librarian',
            'is_active' => true,
        ]);

        // Create regular users
        $users = [
            [
                'name' => 'Kiss János',
                'email' => 'kiss.janos@email.hu',
                'password' => Hash::make('password'),
                'phone' => '+36301234569',
                'address' => 'Budapest, Andrássy út 60.',
                'membership_number' => 'MEM001',
                'membership_type' => 'standard',
                'is_active' => true,
            ],
            [
                'name' => 'Nagy Anna',
                'email' => 'nagy.anna@email.hu',
                'password' => Hash::make('password'),
                'phone' => '+36301234570',
                'address' => 'Budapest, Széchenyi rakpart 5.',
                'membership_number' => 'MEM002',
                'membership_type' => 'student',
                'is_active' => true,
            ],
            [
                'name' => 'Szabó Péter',
                'email' => 'szabo.peter@email.hu',
                'password' => Hash::make('password'),
                'phone' => '+36301234571',
                'address' => 'Budapest, Dohány u. 2.',
                'membership_number' => 'MEM003',
                'membership_type' => 'standard',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create books
        $books = [
            [
                'title' => 'Toldi',
                'isbn' => '9789631234567',
                'author_id' => 1,
                'category_id' => 1,
                'publication_year' => 1847,
                'publisher' => 'Klasszikus Kiadó',
                'pages' => 156,
                'language' => 'magyar',
                'description' => 'Arany János epikus költeménye',
                'total_copies' => 5,
                'available_copies' => 4,
            ],
            [
                'title' => 'János vitéz',
                'isbn' => '9789631234568',
                'author_id' => 2,
                'category_id' => 1,
                'publication_year' => 1845,
                'publisher' => 'Klasszikus Kiadó',
                'pages' => 98,
                'language' => 'magyar',
                'description' => 'Petőfi Sándor verses meséje',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'Harry Potter és a bölcsek köve',
                'isbn' => '9789631234569',
                'author_id' => 3,
                'category_id' => 1,
                'publication_year' => 1997,
                'publisher' => 'Animus Kiadó',
                'pages' => 432,
                'language' => 'magyar',
                'description' => 'A fiú, aki túlélte - első rész',
                'total_copies' => 8,
                'available_copies' => 6,
            ],
            [
                'title' => 'Alapítvány',
                'isbn' => '9789631234570',
                'author_id' => 4,
                'category_id' => 4,
                'publication_year' => 1951,
                'publisher' => 'Sci-Fi Kiadó',
                'pages' => 512,
                'language' => 'magyar',
                'description' => 'Isaac Asimov galaktikus trilógiájának első része',
                'total_copies' => 4,
                'available_copies' => 3,
            ],
            [
                'title' => 'A kék vonat rejtélye',
                'isbn' => '9789631234571',
                'author_id' => 5,
                'category_id' => 1,
                'publication_year' => 1928,
                'publisher' => 'Krimi Kiadó',
                'pages' => 267,
                'language' => 'magyar',
                'description' => 'Hercule Poirot kalandjai',
                'total_copies' => 6,
                'available_copies' => 5,
            ],
        ];

        foreach ($books as $bookData) {
            Book::create($bookData);
        }

        // Create some borrow records
        DB::table('book_borrowings')->insert([
            [
                'user_id' => 3, // Kiss János
                'book_id' => 1, // Toldi
                'borrowed_at' => now()->subDays(10),
                'due_date' => now()->addDays(4),
                'returned_at' => null,
                'notes' => 'Első kölcsönzés',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4, // Nagy Anna
                'book_id' => 3, // Harry Potter
                'borrowed_at' => now()->subDays(5),
                'due_date' => now()->addDays(9),
                'returned_at' => null,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5, // Szabó Péter
                'book_id' => 4, // Alapítvány
                'borrowed_at' => now()->subDays(15),
                'due_date' => now()->subDays(1),
                'returned_at' => null,
                'notes' => 'Meghosszabbítva egyszer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        echo "Database seeding completed successfully!\n";
        echo "Admin credentials: admin@bibliotech.hu / password\n";
        echo "Librarian credentials: librarian@bibliotech.hu / password\n";
    }
}
