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
        return $this->belongsTo(ProductCompany::class, 'company_id');
    }
    //commission
    public function commissions()
    {
        return $this->hasMany(ProductCommission::class);
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

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function priceWithUserType()
    {
        return $this->hasMany(ProductPrice::class)->with('userType');
    }

    public function stock()
    {
        return $this->hasOne(ProductStock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
