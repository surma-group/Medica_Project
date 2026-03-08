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
        'name',
        'generic_name',
        'strength',
        'manufacturer_name',
        'image',
        'status',

        'base_unit_id',
        'secondary_unit_id',
        'conversion_rate',
    ];

    // Optionally cast conversion_rate as float
    protected $casts = [
        'status' => 'boolean',
        'conversion_rate' => 'float',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
