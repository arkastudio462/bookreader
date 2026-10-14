<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private CheckoutService $checkout,
        private MidtransService $midtrans,
    ) {
    }

    public function index()
    {
        $orders = Order::withCount('items')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load('items.book', 'voucher');

        return Inertia::render('Orders/Show', [
            'order' => $order,
            'client_key' => config('perpuskita.midtrans.client_key'),
            'snap_script' => $this->midtrans->snapScriptUrl(),
        ]);
    }

    public function pay(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if (!$order->isPaid()) {
            try {
                $this->checkout->requestSnapToken($order);
            } catch (Throwable $e) {
                report($e);

                return back()->with('error', 'Gagal memuat halaman pembayaran. Coba lagi beberapa saat.');
            }
        }

        return back()->with('success', 'Halaman pembayaran siap.');
    }
}
