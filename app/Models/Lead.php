<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lead extends Model
{
    protected $fillable = [
        'public_id',
        'email',
        'source',
        'book_id',
        'term',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'gclid',
        'referrer',
        'ip_address',
        'consented_at',
        'unsubscribed_at',
        'brevo_contact_id',
        'last_captured_at',
    ];

    protected $casts = [
        'consented_at'     => 'datetime',
        'unsubscribed_at'  => 'datetime',
        'last_captured_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Lead $lead) {
            if (empty($lead->public_id)) {
                $lead->public_id = (string) Str::uuid();
            }
        });
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
