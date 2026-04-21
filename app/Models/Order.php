<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'subtotal',
        'delivery_charge',
        'discount',
        'grand_total',
        'payment_method',
        'status',
        'note',
    ];

    // ✅ belongs to customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // ✅ has many order items (next step)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}