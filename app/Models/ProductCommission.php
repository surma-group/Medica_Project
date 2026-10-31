<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCommission extends Model
{
    protected $fillable = [
        'product_id',
        'user_type_id',
        'commission',
        'commission_type',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
    
    public function isPercentage()
    {
        return $this->commission_type == 1;
    }
}