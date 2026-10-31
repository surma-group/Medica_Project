<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'number',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Type Labels
    public const PERSONAL = 1;
    public const AGENT = 2;
    public const MERCHANT = 3;

    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            self::PERSONAL => 'Personal',
            self::AGENT => 'Agent',
            self::MERCHANT => 'Merchant',
            default => 'Unknown',
        };
    }
}