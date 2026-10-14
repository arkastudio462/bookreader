<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CheckoutService;
use App\Services\MidtransService;
use App\Services\PointService;
use App\Services\PriceResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkout,
        private PriceResolver $prices,
        private PointService $points,
        private MidtransService $midtrans,
    ) {
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

        return Inertia::render('Checkout/Index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'points_balance' => (int) $user->points_balance,
            'max_redeemable' => $this->points->maxRedeemable($user, $subtotal),
            'redeem_rate' => $this->points->redeemRate(),
            'max_redeem_percent' => $this->points->maxRedeemPercent(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'voucher_code' => 'nullable|string|max:64',
            'use_points' => 'boolean',
        ]);

        $order = $this->checkout->place(
            Auth::user(),
            $data['voucher_code'] ?? null,
            (bool) ($data['use_points'] ?? false),
        );

        try {
            $this->checkout->requestSnapToken($order);
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan dibuat, tetapi gagal memuat halaman pembayaran. Silakan coba bayar lagi.');
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan dibuat. Selesaikan pembayaran Anda.');
    }
}
