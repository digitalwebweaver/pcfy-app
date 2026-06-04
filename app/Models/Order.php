<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'order_number', 'quote_id', 'assigned_to',
        'customer_name', 'customer_email', 'customer_phone', 'company_name', 'gstin',
        'billing_address', 'shipping_address',
        'status', 'payment_status', 'payment_notes',
        'total_amount', 'estimated_delivery_date',
        'confirmed_at', 'shipped_at', 'delivered_at', 'notes',
    ];

    protected $casts = [
        'total_amount'            => 'integer',
        'estimated_delivery_date' => 'date',
        'confirmed_at'            => 'datetime',
        'shipped_at'              => 'datetime',
        'delivered_at'            => 'datetime',
    ];

    public function quote()     { return $this->belongsTo(Quote::class); }
    public function assignedTo(){ return $this->belongsTo(User::class, 'assigned_to'); }
    public function items()     { return $this->hasMany(OrderItem::class); }
    public function shipments() { return $this->hasMany(OrderShipment::class)->latest(); }

    public static function generateOrderNumber(): string
    {
        $year = now()->format('Y');
        $last = static::whereYear('created_at', $year)->lockForUpdate()->max('id') ?? 0;
        return 'ORD-' . $year . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

    public static function statuses(): array
    {
        return ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    }

    public static function statusLabel(string $s): string
    {
        return match($s) {
            'pending'    => 'Pending',
            'confirmed'  => 'Confirmed',
            'processing' => 'Processing',
            'shipped'    => 'Shipped',
            'delivered'  => 'Delivered',
            'cancelled'  => 'Cancelled',
            default      => ucfirst($s),
        };
    }

    public static function paymentStatuses(): array { return ['pending', 'partial', 'paid']; }
    public static function paymentLabel(string $s): string
    {
        return match($s) {
            'pending' => 'Payment pending',
            'partial' => 'Partially paid',
            'paid'    => 'Paid',
            default   => ucfirst($s),
        };
    }

    public function getTotalAmountInrAttribute(): string
    {
        return '₹' . number_format($this->total_amount / 100, 0, '.', ',');
    }
}
