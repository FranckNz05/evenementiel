<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    protected $fillable = [
        'custom_event_id', 'first_name', 'last_name', 'is_couple',
        'email', 'phone', 'status', 'has_arrived', 'arrived_at'
    ];

    protected $casts = [
        'is_couple' => 'boolean',
        'has_arrived' => 'boolean',
        'arrived_at' => 'datetime',
    ];

    public function customEvent(): BelongsTo
    {
        return $this->belongsTo(CustomEvent::class);
    }

    public function invitation(): HasOne
    {
        return $this->hasOne(Invitation::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}