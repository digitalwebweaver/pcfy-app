<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'status', 'read_at', 'replied_at'];

    protected $casts = ['read_at' => 'datetime', 'replied_at' => 'datetime'];

    public function scopeUnread($q) { return $q->where('status', 'unread'); }
    public function scopeActive($q) { return $q->whereNotIn('status', ['archived']); }

    public function markRead(): void
    {
        if ($this->status === 'unread') {
            $this->update(['status' => 'read', 'read_at' => now()]);
        }
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(implode('', array_map(fn($p) => $p[0] ?? '', array_slice($parts, 0, 2))));
    }
}
