<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'reference_type',
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
        'amount' => 'decimal:2',
        'approve_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Employee or Partner relation
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Approver
    public function approver()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    // Creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Payment handler
    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Status label
    public function statusLabel(): string
    {
        return match ((int) $this->status) {
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
            default => 'Unknown',
        };
    }
}
