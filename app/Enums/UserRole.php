<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case AUTHOR = 'author';
    case PUBLISHER = 'publisher';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::USER => 'Pembaca',
            self::AUTHOR => 'Penulis',
            self::PUBLISHER => 'Penerbit',
            self::ADMIN => 'Super Admin',
        };
    }

    public function isContentCreator(): bool
    {
        return in_array($this, [self::AUTHOR, self::PUBLISHER, self::ADMIN]);
    }
}
