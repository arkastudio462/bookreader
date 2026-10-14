<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingPreference extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'font_size',
        'font_family',
        'line_height',
        'margin',
    ];

    protected function casts(): array
    {
        return [
            'font_size' => 'integer',
            'line_height' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil preferensi user, atau nilai default bila belum ada.
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'theme' => 'light',
                'font_size' => 16,
                'font_family' => 'serif',
                'line_height' => 1.60,
                'margin' => 'normal',
            ]
        );
    }
}
