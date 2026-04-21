<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderRequest extends Model
{
    use HasFactory;

    protected $table = 'order_requests';

    protected $fillable = [
        'customer_id',
        'order_number',
        'type',
        'prescription_file',
        'prescription_description',
        'total_items',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderRequestItem::class);
    }

    public function getTypeNameAttribute()
    {
        return match($this->type) {
            1 => 'General',
            2 => 'Prescription',
            default => 'Unknown'
        };
    }

    public function getStatusNameAttribute()
    {
        return match($this->status) {
            0 => 'Pending',
            1 => 'Completed',
            2 => 'Processing',
            3 => 'Cancelled',
            default => 'Unknown'
        };
    }
}