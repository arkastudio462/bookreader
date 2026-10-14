<?php

namespace App\Services;

use App\Enums\VoucherType;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Voucher & validasinya (PRD 4.7.b, 7.2).
 */
class VoucherService
{
    public function findActive(string $code): ?Voucher
    {
        return Voucher::whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))])->first();
    }

    public function discountFor(Voucher $voucher, float $subtotal): float
    {
        $discount = match ($voucher->type) {
            VoucherType::PERCENT => $subtotal * (float) $voucher->value / 100,
            VoucherType::FIXED => (float) $voucher->value,
        };

        if ($voucher->max_discount !== null) {
            $discount = min($discount, (float) $voucher->max_discount);
        }

        return round(min($discount, $subtotal), 2);
    }

    /**
     * Validasi voucher untuk isi keranjang (PRD 4.7.6).
     *
     * @param  Collection<int, Book>  $books
     * @return array{voucher: ?Voucher, discount: float}
     */
    public function validateForCart(?string $code, Collection $books, float $subtotal): array
    {
        if (!$code || trim($code) === '') {
            return ['voucher' => null, 'discount' => 0.0];
        }

        $voucher = $this->findActive($code);

        if (!$voucher || !$voucher->isActive()) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Kode voucher tidak valid atau sudah tidak berlaku.',
            ]);
        }

        if ($subtotal < (float) $voucher->min_spend) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Minimal belanja Rp ' . number_format((float) $voucher->min_spend, 0, ',', '.') . ' untuk memakai voucher ini.',
            ]);
        }

        $matches = $books->contains(fn (Book $b) => $voucher->appliesToBook($b));
        if (!$matches) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher tidak berlaku untuk buku di keranjang Anda.',
            ]);
        }

        return ['voucher' => $voucher, 'discount' => $this->discountFor($voucher, $subtotal)];
    }

    /**
     * Catat pemakaian voucher dengan penguncian baris agar kuota tidak terlampaui.
     */
    public function consume(Voucher $voucher, User $user, Order $order, float $discount): void
    {
        DB::transaction(function () use ($voucher, $user, $order, $discount) {
            $locked = Voucher::whereKey($voucher->id)->lockForUpdate()->first();

            if (!$locked || !$locked->isActive()) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Kuota voucher sudah habis atau voucher tidak lagi berlaku.',
                ]);
            }

            $locked->increment('used_count');

            VoucherUsage::create([
                'voucher_id' => $locked->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'discount_amount' => $discount,
            ]);
        });
    }

    /**
     * Lepas satu pemakaian (mis. order gagal/kedaluwarsa).
     */
    public function release(?Voucher $voucher): void
    {
        if (!$voucher) {
            return;
        }

        DB::transaction(function () use ($voucher) {
            $locked = Voucher::whereKey($voucher->id)->lockForUpdate()->first();
            if ($locked && $locked->used_count > 0) {
                $locked->decrement('used_count');
            }
        });
    }
}
