<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipment extends Model
{
    protected $fillable = [
        'order_id', 'courier_name', 'tracking_number', 'tracking_url',
        'status', 'estimated_delivery', 'notes', 'dispatched_at', 'delivered_at',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
        'dispatched_at'      => 'datetime',
        'delivered_at'       => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }

    public static function statuses(): array
    {
        return ['preparing', 'dispatched', 'in_transit', 'out_for_delivery', 'delivered', 'exception'];
    }

    public static function statusLabel(string $s): string
    {
        return match($s) {
            'preparing'         => 'Preparing',
            'dispatched'        => 'Dispatched',
            'in_transit'        => 'In Transit',
            'out_for_delivery'  => 'Out for Delivery',
            'delivered'         => 'Delivered',
            'exception'         => 'Exception',
            default             => ucfirst($s),
        };
    }
}
