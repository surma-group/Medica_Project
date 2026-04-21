<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'district_id',
        'postcode',
        'status',
    ];

    // One customer has many orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ✅ customer belongs to district
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}