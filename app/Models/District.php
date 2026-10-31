<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'division_id',
        'name_en',
        'name_bn',
    ];

    // A district belongs to one division
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    // A district can have many companies
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function upazilas(): HasMany
    {
        return $this->hasMany(Upazila::class);
    }
}
