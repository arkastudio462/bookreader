<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PAID = 'paid';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Persetujuan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::PAID => 'Sudah Dibayar',
        };
    }

    public function isProcessed(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED, self::PAID]);
    }
}
