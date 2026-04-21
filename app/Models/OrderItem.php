<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'total',
        'unit_id',
        'status',
    ];

    /**
     * Relationships
     */

    // belongs to order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // belongs to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // belongs to unit (optional)
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}