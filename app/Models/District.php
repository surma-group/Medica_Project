<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'name_en',
        'name_bn',
    ];

    // A district can have many companies
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
