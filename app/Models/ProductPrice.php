<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = 'product_prices';

    protected $fillable = [
        'product_id',
        'user_type_id',
        'unit_id',
        'price',
        'currency',
        'status',
    ];

    /**
     * Relation: ProductPrice belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
