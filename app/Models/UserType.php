<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use SoftDeletes;

    protected $table = 'user_type';

    protected $fillable = [
        'name',
        'data',
        'status',
    ];

    /**
     * Cast JSON field to array
     */
    protected $casts = [
        'data' => 'array',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_type_id');
    }
}
