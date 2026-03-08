<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'salary_components';

    protected $fillable = [
        'title',
        'payment_type',
        'amount_type',
        'amount',
        'status',
    ];
}
