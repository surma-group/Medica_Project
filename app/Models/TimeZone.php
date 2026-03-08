<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    use HasFactory;

    protected $table = 'time_zones';

    protected $fillable = [
        'name',        // e.g., Asia/Dhaka
        'label',       // e.g., (UTC+06:00) Dhaka
        'utc_offset',  // e.g., +06:00
    ];

    // A timezone can have many companies
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
