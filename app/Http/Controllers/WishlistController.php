<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Services\PriceResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index(PriceResolver $prices)
    {
        $wishlists = Wishlist::with('book.category', 'book.flashSaleItems.flashSale')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->get()
            ->map(function ($w) use ($prices) {
                $w->book->average_rating = round($w->book->averageRating(), 1);
                $prices->decorate($w->book);

                return $w;
            });

        return Inertia::render('Wishlist/Index', [
            'wishlists' => $wishlists,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('book_id', $request->book_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Buku sudah ada di wishlist.');
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
        ]);

        return back()->with('success', 'Buku ditambahkan ke wishlist.');
    }

    public function destroy(int $bookId)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('book_id', $bookId)
            ->delete();

        return back()->with('success', 'Buku dihapus dari wishlist.');
    }
}
