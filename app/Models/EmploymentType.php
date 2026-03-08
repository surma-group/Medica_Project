<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentType extends Model
{
    use SoftDeletes;

    protected $table = 'employment_type'; // Optional, Laravel can infer automatically

    protected $fillable = [
        'title',
        'status',
    ];
}
