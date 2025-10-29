<?php

// ============================================
// app/Models/Category.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'parent_category_id',
        'display_order',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationships
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_category_id')
            ->orderBy('display_order');
    }

    // Scopes
    public function scopeMain($query)
    {
        return $query->whereNull('parent_category_id')
            ->orderBy('display_order');
    }

    public function scopeWithBooks($query)
    {
        return $query->has('books');
    }
}
