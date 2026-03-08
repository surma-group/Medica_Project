<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    // Mass assignable fields
    protected $fillable = [
        'company_code',
        'company_name',
        'company_short_name',
        'tag_line',
        'description',
        'registration_no',
        'trade_license_no',
        'tin_no',
        'bin_vat_no',
        'incorporation_date',
        'email',
        'phone',
        'mobile',
        'website',
        'logo',
        'favicon',
        'address_line_1',
        'address_line_2',
        'district_id',
        'currency_id',
        'timezone_id',
        'financial_year_start',
        'financial_year_end',
        'status',
        'created_by',
    ];

    // Relationships
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function timezone()
    {
        return $this->belongsTo(TimeZone::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branches()
    {
        return $this->hasMany(\App\Models\Branch::class, 'company_id', 'id');
    }
}
