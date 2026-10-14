<?php

namespace App\Services;

use App\Enums\DiscountSource;
use App\Enums\OrderStatus;
use App\Mail\OrderReceiptMail;
use App\Models\FlashSaleItem;
use App\Models\Order;
use App\Models\PaymentWebhook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Pemrosesan webhook Midtrans & pemenuhan order (PRD 4.6.4, 7.2).
 */
class PaymentService
{
    public function __construct(
        private MidtransService $midtrans,
        private PointService $points,
        private VoucherService $vouchers,
    ) {
    }

    /**
     * @return string salah satu: invalid_signature|duplicate|order_not_found|processed
     */
    public function handleWebhook(array $payload): string
    {
        if (!$this->midtrans->verifySignature($payload)) {
            Log::warning('Midtrans webhook: signature tidak valid', [
                'order_id' => $payload['order_id'] ?? null,
            ]);

            return 'invalid_signature';
        }

        $midtransOrderId = (string) ($payload['order_id'] ?? '');
        $eventType = (string) ($payload['transaction_status'] ?? 'unknown');

        // Idempotensi: satu (order, event) hanya diproses sekali (PRD 4.6.4).
        $webhook = PaymentWebhook::firstOrCreate(
            ['midtrans_order_id' => $midtransOrderId, 'event_type' => $eventType],
            ['payload' => $payload, 'signature' => $payload['signature_key'] ?? null],
        );

        if (!$webhook->wasRecentlyCreated) {
            return 'duplicate';
        }

        $order = Order::where('midtrans_order_id', $midtransOrderId)->first();
        if (!$order) {
            $webhook->update(['processed_at' => now()]);

            return 'order_not_found';
        }

        match ($this->midtrans->mapStatus($eventType)) {
            OrderStatus::PAID => $this->markPaid($order),
            OrderStatus::FAILED => $this->markFailed($order),
            OrderStatus::REFUNDED => $this->markRefunded($order),
            default => null,
        };

        $webhook->update(['processed_at' => now()]);

        return 'processed';
    }

    public function markPaid(Order $order): void
    {
        if ($order->isPaid()) {
            return;
        }

        DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            if (!$locked || $locked->isPaid()) {
                return;
            }

            $locked->status = OrderStatus::PAID;
            $locked->paid_at = now();
            $locked->save();

            // Poin pembelian (PRD 4.7.12).
            $earned = $this->points->pointsEarnedFor((float) $locked->total);
            if ($earned > 0) {
                $this->points->earn($locked->user, $earned, 'order', (string) $locked->id, 'Poin pembelian');
            }

            // Catat terjual untuk kampanye flash sale (FR-4.7.11).
            $locked->loadMissing('items');
            foreach ($locked->items as $orderItem) {
                if ($orderItem->discount_source !== DiscountSource::FLASH) {
                    continue;
                }

                FlashSaleItem::where('book_id', $orderItem->book_id)
                    ->whereHas('flashSale', fn ($q) => $q->running())
                    ->increment('sold_count');
            }
        });

        // Notifikasi email kwitansi (PRD 4.6.8) — best effort.
        try {
            $order->refresh();
            if ($order->user?->email) {
                Mail::to($order->user->email)->send(new OrderReceiptMail($order));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function markFailed(Order $order): void
    {
        if ($order->status !== OrderStatus::PENDING) {
            return;
        }

        DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== OrderStatus::PENDING) {
                return;
            }

            $locked->status = OrderStatus::FAILED;
            $locked->save();

            if ((int) $locked->points_used > 0) {
                $this->points->revert($locked->user, (int) $locked->points_used, 'order', (string) $locked->id, 'Order gagal');
            }

            if ($locked->voucher) {
                $this->vouchers->release($locked->voucher);
            }
        });
    }

    public function markRefunded(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            if (!$locked || $locked->status === OrderStatus::REFUNDED) {
                return;
            }

            $wasPaid = $locked->status === OrderStatus::PAID;
            $locked->status = OrderStatus::REFUNDED;
            $locked->save();

            // Poin hangus saat refund (PRD 4.7.15).
            if ($wasPaid) {
                $earned = $this->points->pointsEarnedFor((float) $locked->total);
                $deduct = min($earned, (int) $locked->user->points_balance);
                if ($deduct > 0) {
                    $this->points->spend($locked->user, $deduct, 'order', (string) $locked->id, 'Refund');
                }
            }
        });
    }
}
