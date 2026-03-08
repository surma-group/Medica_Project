<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $fillable = [
        'company_id',
        'branch_code',
        'branch_name',
        'is_head_office',
        'email',
        'mobile',
        'address',
        'district',
        'opening_date',
        'status',
    ];
    
    protected $casts = [
        'opening_date' => 'date',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function districtInfo() // you can name it anything
    {
        return $this->belongsTo(District::class, 'district');
    }
}
