<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'publisher_id',
        'title',
        'slug',
        'description',
        'author_name',
        'isbn',
        'price',
        'discount_price',
        'discount_starts_at',
        'discount_ends_at',
        'sample_price',
        'cover_path',
        's3_key',
        'pages',
        'language',
        'category_id',
        'genre_id',
        'status',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookStatus::class,
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'discount_starts_at' => 'datetime',
            'discount_ends_at' => 'datetime',
            'sample_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('status', 'visible')->where('parent_id', null);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function flashSaleItems()
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    /**
     * Apakah diskon terjadwal sedang berlaku (FR-4.7.1/FR-4.7.2).
     */
    public function hasActiveDiscount(): bool
    {
        if ($this->discount_price === null || (float) $this->discount_price <= 0) {
            return false;
        }

        if ((float) $this->discount_price >= (float) $this->price) {
            return false;
        }

        $now = now();

        if ($this->discount_starts_at !== null && $this->discount_starts_at->greaterThan($now)) {
            return false;
        }

        if ($this->discount_ends_at !== null && $this->discount_ends_at->lessThan($now)) {
            return false;
        }

        return true;
    }

    public function averageRating()
    {
        return $this->ratings()->avg('stars') ?? 0;
    }

    public function ratingCount()
    {
        return $this->ratings()->count();
    }

    public function scopePublished($query)
    {
        return $query->where('status', BookStatus::PUBLISHED);
    }

    public function scopePending($query)
    {
        return $query->where('status', BookStatus::PENDING_REVIEW);
    }

    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function highlights()
    {
        return $this->hasMany(Highlight::class);
    }
}
