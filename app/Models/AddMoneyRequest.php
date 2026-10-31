<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddMoneyRequest extends Model
{
    protected $fillable = [
        'user_id',
        'payment_account_id',
        'amount',
        'transaction_id',
        'note',
        'status',
        'approve_note',
        'approve_by',
        'approve_at',
    ];

    protected $casts = [
        'approve_at' => 'datetime',
    ];

    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }
}
