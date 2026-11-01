<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type_id',
        'reference_id',
        'order_number',
        'subtotal',
        'delivery_charge',
        'discount',
        'grand_total',
        'payment_method',
        'status',
        'note',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // User Type (customer / dealer / agent)
    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    // Unified relation (Customer / Partner reference)
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'reference_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'reference_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SMART ACCESSOR (AUTO DETECT OWNER)
    |--------------------------------------------------------------------------
    */

    public function getOwnerAttribute()
    {
        return match ($this->user_type_id) {

            USER_TYPE_CUSTOMER => $this->customer,

            USER_TYPE_DEALER,
            USER_TYPE_AGENT => $this->partner,

            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            ORDER_STATUS_PENDING => 'Pending',
            ORDER_STATUS_COMPLETED => 'Completed',
            ORDER_STATUS_ACCEPTED => 'Accepted',
            ORDER_STATUS_ON_THE_WAY => 'On The Way',
            ORDER_STATUS_REJECTED => 'Cancelled',
            default => 'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', ORDER_STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', ORDER_STATUS_COMPLETED);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', ORDER_STATUS_ACCEPTED);
    }

    public function scopeOnTheWay($query)
    {
        return $query->where('status', ORDER_STATUS_ON_THE_WAY);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ORDER_STATUS_REJECTED);
    }

    public function transfers()
    {
        return $this->hasMany(OrderTransfer::class, 'order_id');
    }
}
