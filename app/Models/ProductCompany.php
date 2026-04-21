<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;

class ProductCompany extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'company_order',
        'status',
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'company_id');
    }
}
