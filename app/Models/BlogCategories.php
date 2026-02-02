<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BlogCategories extends Model
{
    protected $table = 'blog_categories';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Relations
    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}



