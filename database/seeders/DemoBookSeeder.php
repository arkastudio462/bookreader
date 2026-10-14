<?php

namespace Database\Seeders;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoBookSeeder extends Seeder
{
    public function run(): void
    {
        $publisher = User::where('role', 'publisher')->first();
        $users = User::where('role', 'user')->take(3)->get();

        $books = [
            [
                'title' => 'Filosofi Teras',
                'slug' => 'filosofi-teras',
                'description' => 'Buku yang mengupas filsafat Yunani Kuno dengan cara yang mudah dipahami.',
                'author_name' => 'Henry Manela',
                'isbn' => '978-1234567890',
                'price' => 79000,
                'language' => 'id',
                'category' => 'Non-Fiksi',
                'genre' => 'Filosofi',
                'status' => BookStatus::PUBLISHED,
            ],
            [
                'title' => 'Atomic Habits',
                'slug' => 'atomic-habits',
                'description' => 'Panduan membangun kebiasaan kecil yang membawa perubahan besar.',
                'author_name' => 'James Clear',
                'isbn' => '978-0735211292',
                'price' => 89000,
                'language' => 'id',
                'category' => 'Self-Improvement',
                'genre' => 'Psikologi',
                'status' => BookStatus::PUBLISHED,
            ],
            [
                'title' => 'Sapiens',
                'slug' => 'sapiens',
                'description' => 'Sejarah singkat umat manusia dari Revolusi Kognitif hingga era modern.',
                'author_name' => 'Yuval Noah Harari',
                'isbn' => '978-0062316097',
                'price' => 99000,
                'language' => 'id',
                'category' => 'Sejarah',
                'genre' => 'Sejarah',
                'status' => BookStatus::PUBLISHED,
            ],
        ];

        foreach ($books as $bookData) {
            $category = Category::where('name', $bookData['category'])->first();
            $genre = Genre::where('name', $bookData['genre'])->first();

            $book = Book::create([
                'publisher_id' => $publisher->id,
                'title' => $bookData['title'],
                'slug' => $bookData['slug'],
                'description' => $bookData['description'],
                'author_name' => $bookData['author_name'],
                'isbn' => $bookData['isbn'],
                'price' => $bookData['price'],
                'language' => $bookData['language'],
                'category_id' => $category?->id,
                'genre_id' => $genre?->id,
                'status' => $bookData['status'],
                'published_at' => now(),
            ]);

            // Add demo ratings
            foreach ($users as $user) {
                Rating::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'stars' => rand(4, 5),
                ]);
            }
        }
    }
}
