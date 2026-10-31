<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'order_id',
        'source_transfer_id',
        'from_type',
        'from_id',
        'to_type',
        'to_id',
        'assigned_employee_id',
        'status',
        'sent_at',
        'received_at',
        'note',
        'created_by',
    ];

    protected $casts = [
        'from_type' => 'integer',
        'from_id' => 'integer',
        'to_type' => 'integer',
        'to_id' => 'integer',
        'assigned_employee_id' => 'integer',
        'status' => 'integer',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // Main Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Assigned Transit Officer / Delivery Man
    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    // Products included in this transfer
    public function items()
    {
        return $this->hasMany(OrderTransferItem::class);
    }

    // Transfer status label
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            0 => 'Pending',
            1 => 'Assigned',
            2 => 'Sent',
            3 => 'Received',
            4 => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function sourceTransfer()
    {
        return $this->belongsTo(
            OrderTransfer::class,
            'source_transfer_id'
        );
    }

    public function forwardedTransfer()
    {
        return $this->hasOne(
            OrderTransfer::class,
            'source_transfer_id'
        );
    }
}
