<?php

// ============================================
// app/Models/Author.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'full_name',
        'pseudonyms',
        'biography',
        'birth_date',
        'death_date',
        'nationality',
        'photo_url',
        'slug',
    ];

    protected $casts = [
        'pseudonyms' => 'array',
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($author) {
            if (empty($author->slug)) {
                $author->slug = Str::slug($author->name);
            }
        });
    }

    // Relationships
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author')
            ->withPivot('contribution_type', 'order')
            ->withTimestamps()
            ->orderBy('book_author.order');
    }

    public function booksAsAuthor()
    {
        return $this->belongsToMany(Book::class, 'book_author')
            ->wherePivot('contribution_type', 'author')
            ->withTimestamps();
    }

    public function booksAsTranslator()
    {
        return $this->belongsToMany(Book::class, 'book_author')
            ->wherePivot('contribution_type', 'translator')
            ->withTimestamps();
    }

    // Scopes
    public function scopeBrazilian($query)
    {
        return $query->where('nationality', 'Brazil');
    }

    public function scopeWithBooks($query)
    {
        return $query->has('books');
    }

    // Accessors
    public function getAgeAtDeathAttribute()
    {
        if ($this->birth_date && $this->death_date) {
            return $this->birth_date->diffInYears($this->death_date);
        }
        return null;
    }

    public function getIsPublicDomainAttribute()
    {
        if ($this->death_date) {
            return $this->death_date->diffInYears(now()) >= 70;
        }
        return false;
    }
}



