<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCompany extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'company_order',
        'status',
    ];
}