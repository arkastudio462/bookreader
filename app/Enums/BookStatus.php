<?php

namespace App\Enums;

enum BookStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case PUBLISHED = 'published';
    case REJECTED = 'rejected';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Menunggu Review',
            self::PUBLISHED => 'Terbit',
            self::REJECTED => 'Ditolak',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    public static function editable(): array
    {
        return [self::DRAFT->value, self::REJECTED->value];
    }

    public static function visible(): array
    {
        return [self::PUBLISHED->value];
    }
}
