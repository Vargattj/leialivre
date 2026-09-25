<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLog extends Model
{
    protected $fillable = [
        'signature_valid',
        'headers',
        'payload',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'headers'         => 'array',
    ];
}
