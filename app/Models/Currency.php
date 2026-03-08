<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = [
        'code',       // e.g., BDT, USD
        'name',       // e.g., Taka, US Dollar
        'symbol',     // e.g., ৳, $
    ];

    // A currency can have many companies
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
