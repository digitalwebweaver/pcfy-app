<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Quote extends Model
{
    protected $fillable = [
        'user_id', 'quote_number', 'customer_name', 'customer_email', 'customer_phone',
        'company_name', 'gstin', 'message', 'status', 'assigned_to',
        'estimated_total', 'internal_notes', 'source', 'last_replied_at',
    ];

    protected $casts = ['last_replied_at' => 'datetime'];

    // Relationships
    public function items()      { return $this->hasMany(QuoteItem::class)->orderBy('id'); }
    public function responses()  { return $this->hasMany(QuoteResponse::class)->orderBy('id'); }
    public function order()      { return $this->hasOne(Order::class); }
    public function user()       { return $this->belongsTo(\App\Models\User::class); }
    public function assignedTo() { return $this->belongsTo(\App\Models\User::class, 'assigned_to'); }

    // Computed
    public function getEstimatedTotalInrAttribute(): string
    {
        if (!$this->estimated_total) return '—';
        return '₹' . number_format($this->estimated_total / 100, 0, '.', ',');
    }

    public function getItemsTotalAttribute(): int
    {
        return $this->items->reduce(function ($carry, $item) {
            return $carry + ($item->unit_price ?? 0) * $item->quantity;
        }, 0);
    }

    public function getWhatsappUrlAttribute(): string
    {
        $number  = \App\Models\Setting::get('whatsapp_number', '919876543210');
        $default = \App\Models\Setting::get('whatsapp_message', "Hi, I'm enquiring about Quote #{$this->quote_number} for {$this->customer_name}.");
        $msg     = urlencode($default);
        return "https://wa.me/{$number}?text={$msg}";
    }

    // Scopes
    public function scopeStatus($q, string $s) { return $q->where('status', $s); }
    public function scopeSearch($q, string $term) {
        return $q->where(fn($q) =>
            $q->where('quote_number',   'like', "%{$term}%")
              ->orWhere('customer_name',  'like', "%{$term}%")
              ->orWhere('customer_email', 'like', "%{$term}%")
              ->orWhere('company_name',   'like', "%{$term}%")
        );
    }

    // Static helpers
    public static function generateQuoteNumber(): string
    {
        $year  = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'PCF-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public static function statuses(): array
    {
        return ['new', 'reviewing', 'quoted', 'won', 'lost'];
    }

    public static function statusLabel(string $s): string
    {
        return match($s) {
            'new'       => 'New',
            'reviewing' => 'Reviewing',
            'quoted'    => 'Quoted',
            'won'       => 'Won',
            'lost'      => 'Lost',
            default     => ucfirst($s),
        };
    }
}
