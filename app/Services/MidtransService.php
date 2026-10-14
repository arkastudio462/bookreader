<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Integrasi Midtrans Snap & verifikasi webhook (PRD 4.6.3, 4.6.4).
 *
 * Memakai HTTP client langsung sehingga tidak butuh paket eksternal dan
 * mudah di-fake pada test.
 */
class MidtransService
{
    public function isProduction(): bool
    {
        return (bool) config('perpuskita.midtrans.is_production');
    }

    public function serverKey(): string
    {
        return (string) config('perpuskita.midtrans.server_key');
    }

    public function snapUrl(): string
    {
        if ($url = config('perpuskita.midtrans.snap_url')) {
            return rtrim($url, '/');
        }

        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function snapScriptUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public function configured(): bool
    {
        return $this->serverKey() !== '';
    }

    /**
     * Minta Snap token untuk sebuah order.
     *
     * @return array{token: string, redirect_url: ?string}
     */
    public function createSnapToken(Order $order): array
    {
        $order->loadMissing(['items.book', 'user']);

        $payload = [
            'transaction_details' => [
                'order_id' => $order->midtrans_order_id,
                'gross_amount' => (int) round((float) $order->total),
            ],
            'item_details' => $order->items->map(fn ($item) => [
                'id' => (string) $item->book_id,
                'price' => (int) round((float) $item->price),
                'quantity' => 1,
                'name' => mb_substr((string) ($item->book->title ?? 'Buku'), 0, 50),
            ])->all(),
            'customer_details' => [
                'first_name' => mb_substr((string) ($order->user->name ?? 'Pembaca'), 0, 50),
                'email' => $order->user->email ?? null,
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->post($this->snapUrl(), $payload);

        if ($response->failed()) {
            throw new RuntimeException('Gagal meminta token pembayaran Midtrans: ' . $response->body());
        }

        $data = $response->json();

        return [
            'token' => (string) ($data['token'] ?? ''),
            'redirect_url' => $data['redirect_url'] ?? null,
        ];
    }

    /**
     * Verifikasi signature webhook:
     * sha512(order_id + status_code + gross_amount + server_key).
     */
    public function verifySignature(array $payload): bool
    {
        $signature = $payload['signature_key'] ?? null;
        if (!$signature) {
            return false;
        }

        $expected = hash('sha512',
            (string) ($payload['order_id'] ?? '') .
            (string) ($payload['status_code'] ?? '') .
            (string) ($payload['gross_amount'] ?? '') .
            $this->serverKey()
        );

        return hash_equals($expected, (string) $signature);
    }

    public function mapStatus(string $transactionStatus): OrderStatus
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => OrderStatus::PAID,
            'deny', 'cancel', 'expire', 'failure' => OrderStatus::FAILED,
            'refund', 'partial_refund', 'chargeback' => OrderStatus::REFUNDED,
            default => OrderStatus::PENDING,
        };
    }
}
