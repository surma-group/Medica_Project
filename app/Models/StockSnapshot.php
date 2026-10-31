<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockSnapshot extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'snapshot_date'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
