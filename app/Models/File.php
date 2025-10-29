<?php


// ============================================
// app/Models/File.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'format',
        'size_bytes',
        'size_readable',
        'file_url',
        'backup_url',
        'md5_hash',
        'quality',
        'total_downloads',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByFormat($query, $format)
    {
        return $query->where('format', strtoupper($format));
    }

    // Utility methods
    public function recordDownload()
    {
        $this->increment('total_downloads');
        $this->book->recordDownload();
    }

    public function calculateReadableSize()
    {
        if (!$this->size_bytes) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->size_bytes;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        $this->size_readable = round($bytes, 2) . ' ' . $units[$i];
        $this->save();

        return $this->size_readable;
    }

    // Accessor for format icon
    public function getFormatIconAttribute()
    {
        return match(strtoupper($this->format)) {
            'PDF' => '📄',
            'EPUB' => '📘',
            'MOBI' => '📗',
            'TXT' => '📝',
            default => '📦',
        };
    }
}