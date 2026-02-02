<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_categories',
        'preferred_locations',
        'similar_users',
        'total_views_count',
        'unique_events_viewed',
        'last_preferences_update_at',
        'last_similarity_update_at',
        'last_recommendation_generated_at',
        'view_history_summary',
        'ml_features',
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'preferred_locations' => 'array',
        'similar_users' => 'array',
        'view_history_summary' => 'array',
        'ml_features' => 'array',
        'last_preferences_update_at' => 'datetime',
        'last_similarity_update_at' => 'datetime',
        'last_recommendation_generated_at' => 'datetime',
        'total_views_count' => 'integer',
        'unique_events_viewed' => 'integer',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifie si les préférences doivent être mises à jour
     * (si plus de 24h depuis la dernière mise à jour ou si aucune préférence n'existe)
     */
    public function needsUpdate(): bool
    {
        if (!$this->last_preferences_update_at) {
            return true;
        }

        // Mettre à jour si plus de 24h se sont écoulées
        return $this->last_preferences_update_at->copy()->addHours(24)->isPast();
    }

    /**
     * Vérifie si la similarité doit être recalculée
     */
    public function needsSimilarityUpdate(): bool
    {
        if (!$this->last_similarity_update_at) {
            return true;
        }

        // Recalculer la similarité toutes les 48h (plus long car plus coûteux)
        return $this->last_similarity_update_at->copy()->addHours(48)->isPast();
    }
}
