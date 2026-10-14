<?php

namespace App\Enums;

enum DiscountSource: string
{
    case NONE = 'none';
    case DISCOUNT = 'diskon';
    case FLASH = 'flash';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Tanpa Diskon',
            self::DISCOUNT => 'Diskon Terjadwal',
            self::FLASH => 'Flash Sale',
        };
    }
}
