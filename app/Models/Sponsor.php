<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_path',
        'event_id'
    ];


    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getLogoUrlAttribute()
{
    return $this->logo ? Storage::url($this->logo) : null;
}
}
