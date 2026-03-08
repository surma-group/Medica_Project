<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'salary_month',
        'basic_salary',
        'total_earning',
        'total_deduction',
        'net_salary',
        'status',
        'generated_by',
        'approved_by',
    ];

    protected $casts = [
        'salary_month' => 'date',
    ];

    // 🔹 Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(MonthlySalaryDetail::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
