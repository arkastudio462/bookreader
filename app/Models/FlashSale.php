<?php

namespace App\Models;

use App\Enums\FlashSaleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kampanye Flash Sale (PRD 4.7.c / 6.3).
 */
class FlashSale extends Model
{
    protected $fillable = [
        'name',
        'banner_text',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => FlashSaleStatus::class,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', FlashSaleStatus::ACTIVE);
    }

    /**
     * Kampanye yang sedang berjalan (status aktif + jendela waktu aktif).
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query
            ->where('status', FlashSaleStatus::ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function isRunning(): bool
    {
        return $this->status === FlashSaleStatus::ACTIVE
            && $this->starts_at !== null
            && $this->ends_at !== null
            && $this->starts_at->lessThanOrEqualTo(now())
            && $this->ends_at->greaterThanOrEqualTo(now());
    }

    /**
     * Kampanye yang ditampilkan di landing page.
     */
    public static function current(): ?self
    {
        return static::with(['items.book'])
            ->running()
            ->orderByDesc('starts_at')
            ->first();
    }
}
