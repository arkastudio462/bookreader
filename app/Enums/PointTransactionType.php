<?php

namespace App\Enums;

enum PointTransactionType: string
{
    case EARN = 'earn';
    case SPEND = 'spend';
    case REVERT = 'revert';

    public function label(): string
    {
        return match ($this) {
            self::EARN => 'Poin Masuk',
            self::SPEND => 'Poin Dipakai',
            self::REVERT => 'Poin Dikembalikan',
        };
    }
}
