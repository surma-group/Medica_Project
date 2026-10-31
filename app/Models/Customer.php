<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'ledger_id',
        'first_name',
        'last_name',
        'phone',
        'emergency_contact',
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

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    // optional: user relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
