<?php

namespace App\Models;

use App\Enums\VoucherScope;
use App\Enums\VoucherType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_spend',
        'max_discount',
        'quota',
        'used_count',
        'scope',
        'scope_id',
        'starts_at',
        'expires_at',
        'status',
        'creator_type',
        'creator_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => VoucherType::class,
            'scope' => VoucherScope::class,
            'value' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->quota !== null && $this->used_count >= $this->quota) {
            return false;
        }

        return true;
    }

    /**
     * Apakah voucher berlaku untuk buku tertentu (PRD 4.7.5).
     */
    public function appliesToBook(Book $book): bool
    {
        return match ($this->scope) {
            VoucherScope::GLOBAL => true,
            VoucherScope::BOOK => (int) $this->scope_id === (int) $book->id,
            VoucherScope::PUBLISHER => (int) $this->scope_id === (int) $book->publisher_id,
        };
    }

    public function remainingQuota(): ?int
    {
        return $this->quota === null ? null : max(0, $this->quota - $this->used_count);
    }
}
