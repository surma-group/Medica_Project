<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGeneric extends Model
{
    use SoftDeletes;

    protected $table = 'product_generics';

    protected $fillable = [
        'generic_id',
        'generic_name',
        'precaution',
        'indication',
        'contra_indication',
        'dose',
        'side_effect',
        'mode_of_action',
        'interaction',
        'pregnancy_category_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Example relationship (if you have PregnancyCategory model)
     */
    public function pregnancyCategory()
    {
        return $this->belongsTo(PregnancyCategory::class);
    }
}