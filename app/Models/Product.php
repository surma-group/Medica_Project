<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'category_id',
        'brand_id',
        'company_id',      // ✅ new
        'generic_id',      // ✅ new
        'name',
        'strength',
        'manufacturer_name',
        'image',
        'price',           // ✅ new
        'status',

        'base_unit_id',
        'secondary_unit_id',
        'conversion_rate',
    ];

    protected $casts = [
        'status' => 'boolean',
        'conversion_rate' => 'float',
        'price' => 'float', // ✅ new
    ];

    // ================= Relationships =================

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // ✅ new
    public function company()
    {
        return $this->belongsTo(ProductCompany::class);
    }

    // ✅ new
    public function generic()
    {
        return $this->belongsTo(ProductGeneric::class);
    }

    // Optional (if you use units)
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function secondaryUnit()
    {
        return $this->belongsTo(Unit::class, 'secondary_unit_id');
    }
}