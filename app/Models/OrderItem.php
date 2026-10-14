<?php

namespace App\Models;

use App\Enums\DiscountSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'book_id',
        'price',
        'discount_source',
        'author_share',
        'publisher_share',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'author_share' => 'decimal:2',
            'publisher_share' => 'decimal:2',
            'discount_source' => DiscountSource::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
