<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_transfer_id',
        'order_item_id',
        'quantity_sent',
        'quantity_received',
    ];

    protected $casts = [
        'order_transfer_id' => 'integer',
        'order_item_id' => 'integer',
        'quantity_sent' => 'decimal:3',
        'quantity_received' => 'decimal:3',
    ];

    // Parent Transfer
    public function orderTransfer()
    {
        return $this->belongsTo(OrderTransfer::class);
    }

    // Original Order Item
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
