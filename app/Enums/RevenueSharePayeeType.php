<?php

namespace App\Enums;

enum RevenueSharePayeeType: string
{
    case AUTHOR = 'author';
    case PUBLISHER = 'publisher';

    public function label(): string
    {
        return match($this) {
            self::AUTHOR => 'Penulis',
            self::PUBLISHER => 'Penerbit',
        };
    }
}
