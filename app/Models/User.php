<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasFollowers;
use App\Notifications\VerifyEmailFrench;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, HasFollowers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'genre',
        'tranche_age',
        'password',
        'phone',
        'profile_image',
        'is_profile_complete',
        'address',
        'city',
        'country',
        'is_active',
        'email_verified_at',
        'remember_token',
        'role',
        'provider',
        'provider_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_profile_complete' => 'boolean',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relations
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'user_categories')
            ->withTimestamps();
    }

    public function events()
    {
        return $this->hasManyThrough(Event::class, Organizer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    /**
     * Get the tickets associated with the user.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    /**
     * Get the organizer profile associated with the user.
     */
    public function organizer()
    {
        return $this->hasOne(Organizer::class);
    }

    /**
     * Get all event views by this user.
     */
    public function eventViews()
    {
        return $this->hasMany(EventView::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function favorites()
    {
        return $this->hasMany(EventFavorite::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function followedOrganizers()
    {
        return $this->hasMany(OrganizerFollower::class);
    }

    // Helpers
    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Get the profile completion percentage.
     */
    public function getProfileCompletionPercentageAttribute()
    {
        $fields = ['prenom', 'nom', 'email', 'phone', 'bio', 'profile_image'];
        $completed = 0;

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }

    /**
     * Check if the profile is complete.
     */
    public function checkProfileComplete()
    {
        $this->is_profile_complete = $this->profile_completion_percentage === 100;
        $this->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailFrench);
    }

    /**
     * Get the URL of the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_image ? Storage::url($this->profile_image) : asset('images/default-profile.png');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isFollowing($organizer)
    {
        if ($organizer instanceof Organizer) {
            return $this->followedOrganizers()
                ->where('organizer_id', $organizer->id)
                ->exists();
        }
        return false;
    }
}
