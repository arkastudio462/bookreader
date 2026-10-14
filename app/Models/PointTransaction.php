<?php

namespace App\Models;

use App\Enums\PointTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'points',
        'balance_after',
        'source',
        'reference_id',
        'note',
    ];

    protected function casts(): array
    {
        return ['type' => PointTransactionType::class];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
