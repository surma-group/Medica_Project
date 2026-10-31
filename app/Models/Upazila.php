<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upazila extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'name_en',
        'name_bn',
    ];

    // An upazila belongs to one district
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}