<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrganizerAccessCode extends Model
{
    use HasFactory;

    protected $table = 'organizer_access_codes';

    protected $fillable = [
        'organizer_id',
        'event_id',
        'access_code',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec l'organisateur
     */
public function organizer()
{
    return $this->belongsTo(Organizer::class, 'organizer_id', 'id');
}

    /**
     * Relation avec l'événement
     */
      public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    /**
     * Relation avec l'utilisateur qui a créé le code
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Vérifie si le code est valide
     */
    public function isValid()
    {
        $now = now();
        return $this->is_active &&
               $now->greaterThanOrEqualTo($this->valid_from) &&
               $now->lessThanOrEqualTo($this->valid_until);
    }

    protected static function booted()
{
    static::creating(function ($code) {
        // Générer un code d'accès unique si non fourni
        if (empty($code->access_code)) {
            $code->access_code = Str::upper(Str::random(8));
        }

        // Définir la date de fin si non fournie
        if (empty($code->valid_until)) {
            $event = Event::find($code->event_id);
            if ($event) {
                $code->valid_until = $event->end_date;
            }
        }
    });
}

/**
 * Scope pour les codes valides
 */
public function scopeValid($query)
{
    return $query->where('is_active', true)
        ->where('valid_from', '<=', now())
        ->where('valid_until', '>=', now());
}
}


