<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'from'   => 'datetime',
        'to'     => 'datetime',
        'status' => 'boolean',
    ];
    // Fields that can be mass-assigned
    protected $fillable = [
        'title',
        'from',
        'to',
        'status',
    ];
}
