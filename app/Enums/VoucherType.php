<?php

namespace App\Enums;

enum VoucherType: string
{
    case PERCENT = 'percent';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match($this) {
            self::PERCENT => 'Persentase',
            self::FIXED => 'Nominal',
        };
    }
}
