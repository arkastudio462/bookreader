<?php

namespace App\Enums;

enum PartnershipStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case ENDED = 'ended';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Persetujuan',
            self::ACTIVE => 'Aktif',
            self::ENDED => 'Berakhir',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
