<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\FlashSale;
use App\Services\PriceResolver;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function index(PriceResolver $prices): Response
    {
        // Featured books
        $featuredBooks = $prices->decorateMany(
            Book::published()
                ->where('is_featured', true)
                ->with('flashSaleItems.flashSale')
                ->limit(6)
                ->get()
                ->each(fn (Book $book) => $book->average_rating = round($book->averageRating(), 1))
        );

        // Newest books
        $newestBooks = $prices->decorateMany(
            Book::published()
                ->with('flashSaleItems.flashSale')
                ->latest('published_at')
                ->limit(8)
                ->get()
                ->each(fn (Book $book) => $book->average_rating = round($book->averageRating(), 1))
        );

        // Flash sale aktif dengan hitung mundur (FR-4.1.2 / FR-4.7.9)
        $flashSale = FlashSale::current();

        if ($flashSale !== null) {
            $flashSale->items->each(function ($item) use ($prices) {
                if ($item->book !== null) {
                    $prices->decorate($item->book);
                }
                $item->sold_percent = $item->soldPercent();
            });
        }

        // Popular categories
        $popularCategories = Category::where('is_active', true)
            ->withCount('books')
            ->orderByDesc('books_count')
            ->limit(6)
            ->get();

        // Platform stats
        $stats = [
            'total_books' => Book::published()->count(),
            'total_authors' => DB::table('users')->whereIn('role', ['author', 'publisher'])->count(),
            'total_readers' => DB::table('users')->where('role', 'user')->count(),
        ];

        return Inertia::render('Landing', [
            'featuredBooks' => $featuredBooks,
            'newestBooks' => $newestBooks,
            'flashSale' => $flashSale,
            'popularCategories' => $popularCategories,
            'stats' => $stats,
        ]);
    }
}
