<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type_id',
        'reference_id',
        'created_by',
        'sale_number',
        'subtotal',
        'delivery_charge',
        'discount',
        'grand_total',
        'payment_method',
        'status',
        'note',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}