<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Novel', 'Non-Fiksi', 'Akademik', 'Anak-Anak',
            'Bisnis & Ekonomi', 'Teknologi', 'Self-Improvement',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        $genres = [
            'Fiksi' => ['Romansa', 'Thriller & Misteri', 'Sci-Fi & Fantasy', 'Horor', 'Historical'],
            'Non-Fiksi' => ['Biografi', 'Sejarah', 'Sains', 'Psikologi', 'Religi'],
        ];

        foreach ($genres as $parent => $children) {
            $parentGenre = Genre::firstOrCreate(
                ['slug' => Str::slug($parent)],
                ['name' => $parent]
            );

            foreach ($children as $child) {
                Genre::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    ['name' => $child, 'parent_id' => $parentGenre->id]
                );
            }
        }
    }
}
