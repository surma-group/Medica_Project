<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_type_id',
        'reference_id',
        'purchase_number',
        'subtotal',
        'delivery_charge',
        'discount',
        'grand_total',
        'payment_method',
        'status',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'reference_id');
    }
}
