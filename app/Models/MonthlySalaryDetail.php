<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySalaryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_salary_id',
        'salary_component_id',
        'component_type',
        'amount_type',
        'amount',
        'calculated_amount',
    ];

    // 🔹 Relations
    public function monthlySalary()
    {
        return $this->belongsTo(MonthlySalary::class);
    }

    public function component()
    {
        return $this->belongsTo(SalaryComponent::class, 'salary_component_id');
    }
}
