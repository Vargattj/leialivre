<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    protected $fillable = [
        'event_type',
        'book_id',
        'file_id',
        'ip_address',
        'created_at',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
