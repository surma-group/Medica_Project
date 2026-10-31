<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmploymentType;
use App\Models\District;
use App\Models\WithdrawRequest;
use App\Models\EmployeeRelation;

class Employee extends Model
{
    use HasFactory;

    protected $casts = [
        'joining_date' => 'datetime',
        'date_of_birth' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($employee) {
            if (empty($employee->account_number)) {
                do {
                    $accountNumber = now()->format('Ymd')
                        . str_pad(
                            (string) random_int(0, 9999),
                            4,
                            '0',
                            STR_PAD_LEFT
                        );
                } while (
                    static::where(
                        'account_number',
                        $accountNumber
                    )->exists()
                );

                $employee->account_number = $accountNumber;
            }
        });
    }

    protected $fillable = [
        'company_id',
        'branch_id',
        'department_id',
        'designation_id',
        'employment_type',
        'district',
        'employee_code',
        'account_number',
        'user_id',
        'ledger_id',
        'first_name',
        'last_name',
        'full_name',
        'gender',
        'date_of_birth',
        'joining_date',
        'personal_email',
        'official_email',
        'mobile',
        'phone',
        'permanent_address',
        'present_address',
        'photo',
        'nid_no',
        'passport_no',
        'joining_letter',
        'resume',
        'other_documents',
        'security_deposit_type',
        'security_deposit_file',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'status',
        'created_by',
    ];


    // =========================
    // Basic Relationships
    // =========================

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * IMPORTANT:
     * employment_type table name is NOT plural
     */
    public function employmentType()
    {
        return $this->belongsTo(
            EmploymentType::class,
            'employment_type',
            'id'
        );
    }

    public function districtInfo()
    {
        return $this->belongsTo(
            District::class,
            'district',
            'id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =========================
    // Salary / Accounting
    // =========================

    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }

    public function monthlySalaries()
    {
        return $this->hasMany(MonthlySalary::class);
    }

    public function withdrawRequests()
    {
        return $this->morphMany(WithdrawRequest::class, 'reference');
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }


    public function parentRelations()
    {
        return $this->hasMany(
            EmployeeRelation::class,
            'child_employee_id'
        );
    }


    public function childRelations()
    {
        return $this->hasMany(
            EmployeeRelation::class,
            'parent_employee_id'
        );
    }
}
