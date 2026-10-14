<?php

namespace App\Enums;

enum CommentStatus: string
{
    case VISIBLE = 'visible';
    case HIDDEN = 'hidden';
    case DELETED = 'deleted';

    public function label(): string
    {
        return match($this) {
            self::VISIBLE => 'Tampil',
            self::HIDDEN => 'Tersembunyi',
            self::DELETED => 'Dihapus',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::VISIBLE;
    }
}
