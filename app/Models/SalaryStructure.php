<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'employee_id',
        'basic_salary',
        'total_earning',
        'total_deduction',
        'net_salary',
        'created_by',
    ];

    // 🔗 Relationships

    public function details()
    {
        return $this->hasMany(SalaryStructureDetail::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
