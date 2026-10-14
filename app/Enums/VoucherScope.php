<?php

namespace App\Enums;

enum VoucherScope: string
{
    case GLOBAL = 'global';
    case PUBLISHER = 'publisher';
    case BOOK = 'book';

    public function label(): string
    {
        return match($this) {
            self::GLOBAL => 'Global',
            self::PUBLISHER => 'Penerbit',
            self::BOOK => 'Buku',
        };
    }
}
