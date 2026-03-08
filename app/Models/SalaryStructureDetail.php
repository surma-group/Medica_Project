<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructureDetail extends Model
{
    protected $fillable = [
        'salary_structure_id',
        'salary_component_id',
        'component_type',
        'amount_type',
        'amount',
        'calculated_amount',
    ];

    // 🔗 Relationships

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function component()
    {
        return $this->belongsTo(SalaryComponent::class, 'salary_component_id');
    }
}
