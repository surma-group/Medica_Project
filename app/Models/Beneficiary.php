<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'reference_type',
        'bank_name',
        'branch_name',
        'account_type',
        'account_number',

        'bkash_number',
        'rocket_number',
        'nagad_number',
    ];

    // Relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
