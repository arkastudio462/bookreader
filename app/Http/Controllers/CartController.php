<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Cart;
use App\Services\PriceResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(private PriceResolver $prices)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $cart = Cart::forUser($user);

        $items = $cart->items()->with('book.category')->get()->map(function ($item) {
            if ($item->book) {
                $item->book->effective_price = $this->prices->effectivePrice($item->book);
            }

            return $item;
        });

        $subtotal = round((float) $items->sum(fn ($item) => (float) ($item->book->effective_price ?? 0)), 2);

        return Inertia::render('Cart/Index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'points_balance' => (int) $user->points_balance,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($data['book_id']);

        if ($book->status !== BookStatus::PUBLISHED) {
            return back()->with('error', 'Buku belum tersedia untuk dibeli.');
        }

        if ((float) $book->price <= 0) {
            return back()->with('error', 'Buku ini gratis — langsung baca saja.');
        }

        if ($user->ownsBook($book->id)) {
            return back()->with('error', 'Anda sudah memiliki buku ini.');
        }

        $cart = Cart::forUser($user);

        $cart->items()->firstOrCreate(
            ['book_id' => $book->id],
            ['price_snapshot' => $this->prices->effectivePrice($book)],
        );

        return back()->with('success', 'Buku ditambahkan ke keranjang.');
    }

    public function destroy(int $book)
    {
        $cart = Cart::forUser(Auth::user());
        $cart->items()->where('book_id', $book)->delete();

        return back()->with('success', 'Buku dihapus dari keranjang.');
    }
}
