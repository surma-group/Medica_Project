<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'method',
        'amount',
        'data',
        'note',
        'admin_note',
        'status',
        'approve_by',
        'approve_at',
        'paid_by',
        'paid_at',
        'created_by',
    ];


    protected $casts = [
        'data' => 'array',
        'approve_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel()
    {
        return match ($this->status) {
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
            default => 'Unknown',
        };
    }
}
