<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteResponse extends Model
{
    protected $fillable = [
        'quote_id', 'user_id', 'type', 'subject', 'body',
        'direction', 'is_internal', 'sent_at',
        'attachment_path', 'attachment_name',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'sent_at'     => 'datetime',
    ];

    public function quote() { return $this->belongsTo(Quote::class); }
    public function user()  { return $this->belongsTo(\App\Models\User::class); }
}
