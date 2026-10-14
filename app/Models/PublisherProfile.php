<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublisherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'imprint_name',
        'legal_name',
        'address',
        'phone',
        'npwp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
