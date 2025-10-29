<?php

// ============================================
// app/Models/Tag.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'usage_count',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // Relationships
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_tag')
            ->withTimestamps();
    }

    // Scopes
    public function scopePopular($query, $limit = 20)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    // Method to increment usage
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
