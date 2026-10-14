<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu buku dalam kampanye flash sale (PRD 6.3).
 */
class FlashSaleItem extends Model
{
    protected $fillable = [
        'flash_sale_id',
        'book_id',
        'sale_price',
        'sold_count',
        'stock_limit',
    ];

    protected function casts(): array
    {
        return [
            'sale_price' => 'decimal:2',
            'sold_count' => 'integer',
            'stock_limit' => 'integer',
        ];
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isSoldOut(): bool
    {
        return $this->stock_limit !== null && $this->sold_count >= $this->stock_limit;
    }

    /**
     * Item ini aktif bila kampanyenya sedang berjalan dan stok belum habis.
     */
    public function isActiveNow(): bool
    {
        return $this->flashSale?->isRunning() === true && ! $this->isSoldOut();
    }

    public function remainingStock(): ?int
    {
        if ($this->stock_limit === null) {
            return null;
        }

        return max(0, $this->stock_limit - $this->sold_count);
    }

    /**
     * Persentase terjual untuk tampilan "terjual X%" (FR-4.7.11).
     */
    public function soldPercent(): int
    {
        if ($this->stock_limit === null || $this->stock_limit <= 0) {
            return 0;
        }

        return (int) min(100, round($this->sold_count / $this->stock_limit * 100));
    }
}
