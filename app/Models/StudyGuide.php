<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudyGuide extends Model
{
    protected $fillable = [
        'book_id',
        'slug',
        'title',
        'free_pdf_path',
        'checkout_url',
        'price_cents',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'price_cents'  => 'integer',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Disco onde o PDF do guia vive. Em produção é o R2; sem credenciais
     * configuradas (dev/teste local) cai para o disco "public". Upload no
     * admin e download no funil resolvem o disco por aqui — se cada lado
     * decidisse sozinho, um gravaria num disco e o outro leria de outro.
     */
    public static function disk(): string
    {
        return filled(config('filesystems.disks.r2.key')) ? 'r2' : 'public';
    }

    public function hasFile(): bool
    {
        if (blank($this->free_pdf_path)) {
            return false;
        }

        try {
            return Storage::disk(static::disk())->exists($this->free_pdf_path);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
