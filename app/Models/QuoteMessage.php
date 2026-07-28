<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteMessage extends Model
{
    protected $fillable = [
        'quote_request_id',
        'direction',
        'to_email',
        'from_email',
        'subject',
        'body',
        'status',
        'error',
    ];

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }
}
