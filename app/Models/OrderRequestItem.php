<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderRequestItem extends Model
{
    use HasFactory;

    protected $table = 'order_request_items';

    protected $fillable = [
        'order_request_id',
        'product_name',
        'strength',
        'unit_id', // ✅ updated
        'quantity',
        // 'status', ❗ optional (recommend remove)
    ];

    protected $casts = [
        'quantity' => 'integer',
        // 'status' => 'boolean', // optional
    ];

    /*
    |----------------------------------
    | RELATIONSHIP
    |----------------------------------
    */

    // Order
    public function orderRequest()
    {
        return $this->belongsTo(OrderRequest::class);
    }

    // Unit (NEW ✅)
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /*
    |----------------------------------
    | ACCESSORS
    |----------------------------------
    */

    // Nice display নাম (UI এর জন্য)
    public function getDisplayNameAttribute()
    {
        if ($this->strength) {
            return "{$this->product_name} ({$this->strength})";
        }

        return $this->product_name;
    }

    // Unit title shortcut
    public function getUnitNameAttribute()
    {
        return $this->unit?->title ?? '-';
    }

    /*
    |----------------------------------
    | (OPTIONAL) STATUS ACCESSOR
    |----------------------------------
    */

    // যদি status রাখো, তাহলে এটা রাখো
    public function getStatusNameAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}