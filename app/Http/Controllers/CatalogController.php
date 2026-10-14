<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;
use App\Services\PriceResolver;
use App\Services\ReaderAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request, PriceResolver $prices): Response
    {
        $query = Book::with(['category', 'genre', 'publisher', 'flashSaleItems.flashSale'])
            ->published();

        // Filter
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('genre')) {
            $query->whereHas('genre', fn($q) => $q->where('slug', $request->genre));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter "sedang promo" (FR-4.3.1): diskon terjadwal aktif atau flash sale.
        if ($request->boolean('promo')) {
            $query->where(function ($q) {
                $q->where(function ($d) {
                    $d->whereNotNull('discount_price')
                        ->whereColumn('discount_price', '<', 'books.price')
                        ->where(fn ($w) => $w->whereNull('discount_starts_at')->orWhere('discount_starts_at', '<=', now()))
                        ->where(fn ($w) => $w->whereNull('discount_ends_at')->orWhere('discount_ends_at', '>=', now()));
                })->orWhereHas('flashSaleItems', fn ($f) => $f->whereHas('flashSale', fn ($fs) => $fs->running()));
            });
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('author_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'bestseller' => $query->orderByDesc('id'), // placeholder: nanti order by sales
            'newest' => $query->latest('published_at'),
            'cheapest' => $query->orderBy('price'),
            'rating' => $query->orderByDesc('id'), // placeholder: nanti withAvg
            default => $query->latest('published_at'),
        };

        $books = $query->paginate(12)->withQueryString();

        // Append computed fields (harga efektif + rating)
        $books->getCollection()->transform(function (Book $book) use ($prices) {
            $book->average_rating = round($book->averageRating(), 1);
            $book->rating_count = $book->ratingCount();

            return $prices->decorate($book);
        });

        return Inertia::render('Catalog/Index', [
            'books' => $books,
            'categories' => Category::where('is_active', true)->get(),
            'genres' => Genre::where('is_active', true)->get(),
            'filters' => $request->only(['category', 'genre', 'min_price', 'max_price', 'search', 'sort', 'promo']),
        ]);
    }

    public function show(string $slug, ReaderAccessService $access, PriceResolver $prices): Response
    {
        $book = Book::with(['category', 'genre', 'publisher', 'ratings', 'comments.user', 'flashSaleItems.flashSale'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $book->average_rating = round($book->averageRating(), 1);
        $book->rating_count = $book->ratingCount();
        $prices->decorate($book);

        // Info flash sale (harga + waktu berakhir untuk countdown).
        $flashItem = $book->flashSaleItems->first(fn ($item) => $item->isActiveNow());
        $book->flash_ends_at = $flashItem?->flashSale?->ends_at;
        $book->flash_sold_percent = $flashItem?->soldPercent();

        $relatedBooks = $prices->decorateMany(
            Book::published()
                ->where('category_id', $book->category_id)
                ->where('id', '!=', $book->id)
                ->with('flashSaleItems.flashSale')
                ->limit(4)
                ->get()
                ->each(fn (Book $b) => $b->average_rating = round($b->averageRating(), 1))
        );

        return Inertia::render('Catalog/Show', [
            'book' => $book,
            'relatedBooks' => $relatedBooks,
            'accessMode' => $access->resolve(Auth::user(), $book)['mode'],
        ]);
    }
}
