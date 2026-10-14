<?php

namespace App\Enums;

enum FlashSaleStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case ENDED = 'ended';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Dijadwalkan',
            self::ACTIVE => 'Berjalan',
            self::ENDED => 'Selesai',
        };
    }
}
