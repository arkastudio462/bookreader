<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Alur checkout (PRD 7.2): validasi keranjang, hitung harga efektif,
 * voucher & poin, buat order pending, lalu minta Snap token.
 */
class CheckoutService
{
    public function __construct(
        private PriceResolver $prices,
        private VoucherService $vouchers,
        private PointService $points,
        private MidtransService $midtrans,
    ) {
    }

    public function place(User $user, ?string $voucherCode, bool $usePoints): Order
    {
        $cart = Cart::forUser($user);
        $cart->load('items.book');
        $items = $cart->items;

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Keranjang Anda kosong.']);
        }

        /** @var \Illuminate\Support\Collection<int, \App\Models\Book> $books */
        $books = $items->map(fn ($item) => $item->book)->filter()->values();

        foreach ($books as $book) {
            if ($book->status !== BookStatus::PUBLISHED) {
                throw ValidationException::withMessages([
                    'cart' => "Buku \"{$book->title}\" belum tersedia untuk dibeli.",
                ]);
            }
            if ($user->ownsBook($book->id)) {
                throw ValidationException::withMessages([
                    'cart' => "Anda sudah memiliki buku \"{$book->title}\".",
                ]);
            }
        }

        // Harga efektif dihitung ulang server-side (PRD 7.2 langkah 1).
        $resolved = $items->map(fn ($item) => [
            'book' => $item->book,
            'price' => $this->prices->effectivePrice($item->book),
            'source' => $this->prices->discountSource($item->book),
        ]);

        $subtotal = round((float) $resolved->sum('price'), 2);

        $voucherResult = $this->vouchers->validateForCart($voucherCode, $books, $subtotal);
        $voucher = $voucherResult['voucher'];
        $voucherDiscount = $voucherResult['discount'];

        $afterVoucher = max(0, round($subtotal - $voucherDiscount, 2));

        $pointsUsed = 0;
        $pointDiscount = 0.0;
        if ($usePoints) {
            $pointsUsed = $this->points->maxRedeemable($user, $afterVoucher);
            $pointDiscount = min($this->points->pointsToValue($pointsUsed), $afterVoucher);

            // Jika nilai poin melebihi sisa tagihan, kurangi jumlah poinnya.
            if ($pointDiscount >= $afterVoucher && $afterVoucher > 0) {
                $pointsUsed = $this->points->valueToPoints($afterVoucher);
                $pointDiscount = $this->points->pointsToValue($pointsUsed);
            }
            $pointDiscount = round($pointDiscount, 2);
        }

        $total = max(0, round($afterVoucher - $pointDiscount, 2));

        return DB::transaction(function () use (
            $user, $cart, $resolved, $subtotal, $voucher, $voucherDiscount, $pointsUsed, $pointDiscount, $total
        ) {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'voucher_discount' => $voucherDiscount,
                'point_discount' => $pointDiscount,
                'total' => $total,
                'status' => OrderStatus::PENDING,
                'midtrans_order_id' => 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),
                'voucher_id' => $voucher?->id,
                'points_used' => $pointsUsed,
            ]);

            foreach ($resolved as $row) {
                $order->items()->create([
                    'book_id' => $row['book']->id,
                    'price' => $row['price'],
                    'discount_source' => $row['source'],
                ]);
            }

            if ($voucher) {
                $this->vouchers->consume($voucher, $user, $order, $voucherDiscount);
            }

            if ($pointsUsed > 0) {
                $this->points->spend($user, $pointsUsed, 'order', (string) $order->id, 'Potongan checkout');
            }

            $cart->items()->delete();

            return $order;
        });
    }

    /**
     * Minta Snap token; dipanggil terpisah dari pembuatan order agar
     * kegagalan jaringan tidak membatalkan order yang sudah tercatat.
     */
    public function requestSnapToken(Order $order): void
    {
        $result = $this->midtrans->createSnapToken($order);

        $order->forceFill(['snap_token' => $result['token'] ?: null])->save();
    }
}
