<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Lunas',
            self::FAILED => 'Gagal',
            self::REFUNDED => 'Dikembalikan',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }
}
