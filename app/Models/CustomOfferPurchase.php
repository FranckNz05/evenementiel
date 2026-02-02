<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomOfferPurchase extends Model
{
    protected $fillable = ['user_id', 'plan', 'price', 'operator', 'phone', 'used_at', 'transaction_id', 'status'];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


