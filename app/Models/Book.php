<?php

// ============================================
// app/Models/Book.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'original_title',
        'slug',
        'publication_year',
        'original_publisher',
        'original_language',
        'synopsis',
        'full_description',
        'isbn',
        'pages',
        'is_public_domain',
        'public_domain_year',
        'public_domain_justification',
        'cover_url',
        'cover_thumbnail_url',
        'total_downloads',
        'views',
        'average_rating',
        'total_ratings',
        'is_featured',
        'is_active',
        // Google Books fields
        'google_books_description',
        'google_books_categories',
        'google_books_page_count',
        'google_books_average_rating',
        'google_books_ratings_count',
        'google_books_published_date',
        'google_books_cover_thumbnail_url',
        // OpenLibrary fields
        'openlibrary_description',
        'openlibrary_isbn',
        'openlibrary_publisher',
        'openlibrary_first_publish_year',
        'openlibrary_cover_id',
        'openlibrary_cover_thumbnail_url',
        // Gutendex fields
        'gutendex_description',
        'gutendex_subjects',
        'gutendex_bookshelves',
        'gutendex_download_count',
        // Wikipedia fields
        'wikipedia_description',
        'wikipedia_cover_thumbnail_url',
    ];

    protected $casts = [
        'is_public_domain' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'average_rating' => 'decimal:2',
        'google_books_categories' => 'array',
        'google_books_average_rating' => 'decimal:2',
        'gutendex_subjects' => 'array',
        'gutendex_bookshelves' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });

        static::updated(function ($book) {
            // Update tag usage_count when book is updated
            if ($book->isDirty('tags')) {
                $book->tags->each->incrementUsage();
            }
        });
    }

    // Relationships
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author')
            ->withPivot('contribution_type', 'order')
            ->withTimestamps()
            ->orderBy('book_author.order');
    }

    public function mainAuthors()
    {
        return $this->belongsToMany(Author::class, 'book_author')
            ->wherePivot('contribution_type', 'author')
            ->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function primaryCategory()
    {
        return $this->belongsToMany(Category::class, 'book_category')
            ->wherePivot('is_primary', true)
            ->withTimestamps();
    }

    public function getPrimaryCategoryAttribute()
    {
        return $this->categories()->wherePivot('is_primary', true)->first();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'book_tag')
            ->withTimestamps();
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function activeFiles()
    {
        return $this->hasMany(File::class)->where('is_active', true);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeMostDownloaded($query, $limit = 10)
    {
        return $query->orderBy('total_downloads', 'desc')->limit($limit);
    }

    public function scopeTopRated($query, $limit = 10)
    {
        return $query->where('total_ratings', '>', 0)
            ->orderBy('average_rating', 'desc')
            ->limit($limit);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('publication_year', $year);
    }

    public function scopeBrazilian($query)
    {
        return $query->whereHas('authors', function ($q) {
            $q->where('nationality', 'Brazil');
        });
    }

    public function scopeSearch($query, $term)
    {
        if (is_null($term) || trim($term) === '') {
            return $query;
        }

        $term = trim($term);
        $words = explode(' ', $term);

        return $query->where(function ($q) use ($words) {
            foreach ($words as $word) {
                if (empty($word)) continue;
                
                $q->where(function ($sub) use ($word) {
                    $sub->where('title', 'ILIKE', "%{$word}%")
                        ->orWhere('subtitle', 'ILIKE', "%{$word}%")
                        ->orWhere('synopsis', 'ILIKE', "%{$word}%")
                        ->orWhere('full_description', 'ILIKE', "%{$word}%")
                        ->orWhereHas('authors', function ($authorQuery) use ($word) {
                            $authorQuery->where('name', 'ILIKE', "%{$word}%")
                                        ->orWhere('full_name', 'ILIKE', "%{$word}%")
                                        ->orWhere('pseudonyms', 'ILIKE', "%{$word}%");
                        });
                });
            }
        });
    }

    // Accessors
    public function getFullTitleAttribute()
    {
        if ($this->subtitle) {
            return "{$this->title}: {$this->subtitle}";
        }
        return $this->title;
    }

    public function getAuthorsNamesAttribute()
    {
        return $this->mainAuthors->pluck('name')->join(', ');
    }

    public function getAvailableFormatsAttribute()
    {
        return $this->activeFiles->pluck('format')->unique()->values();
    }

    // Utility methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function recordDownload()
    {
        $this->increment('total_downloads');
    }
}
