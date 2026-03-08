<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'created_by',
    ];

    // Optional: relationship with user
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
