<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'start_date', 'end_date',
        'adresse', 'ville', 'pays', 'adresse_map', 'image',
        'is_approved', 'status', 'category_id', 'organizer_id',
        'etat', 'publish_at', 'is_published', 'event_type', 'keywords'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'publish_at' => 'datetime',
        'is_approved' => 'boolean',
        'is_published' => 'boolean',
        'keywords' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }

    public function zones()
    {
        return $this->hasMany(EventZone::class);
    }

    public function favorites()
    {
        return $this->hasMany(EventFavorite::class);
    }

    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, Ticket::class);
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewed', 'viewed_type', 'viewable_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function shares()
    {
        return $this->hasMany(Share::class, 'event_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('publish_at', '<=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('d M Y H:i');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('d M Y H:i');
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffForHumans($this->end_date, true);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Gratuit' => '<span class="badge bg-success">Gratuit</span>',
            'Payant' => '<span class="badge bg-primary">Payant</span>',
            default => '<span class="badge bg-secondary">Non défini</span>'
        };
    }

    public function getEtatBadgeAttribute()
    {
        return match($this->etat) {
            'En cours' => '<span class="badge bg-success">En cours</span>',
            'Archivé' => '<span class="badge bg-secondary">Archivé</span>',
            'Annulé' => '<span class="badge bg-danger">Annulé</span>',
            default => '<span class="badge bg-warning">Non défini</span>'
        };
    }

    public function isUpcoming()
    {
        return $this->start_date->isFuture();
    }

    public function isPast()
    {
        return $this->end_date->isPast();
    }

    public function isOngoing()
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function hasAvailableTickets()
    {
        return $this->tickets()
                    ->where('statut', 'disponible')
                    ->where('quantite', '>', 0)
                    ->exists();
    }

    public function isFavorited()
    {
        if (!auth()->check()) return false;
        return $this->favorites()->where('user_id', auth()->id())->exists();
    }

    public function canBeBooked()
    {
        return $this->isUpcoming() &&
               $this->is_approved &&
               $this->is_published &&
               $this->hasAvailableTickets();
    }
}
