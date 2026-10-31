<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingMemberCommission extends Model
{
    protected $table = 'marketing_member_commissions';

    protected $fillable = [
        'member_type',
        'member_id',
        'commission_type',
        'commission_amount',
    ];

    protected $casts = [
        'member_type' => 'integer',
        'member_id' => 'integer',
        'commission_type' => 'integer',
        'commission_amount' => 'decimal:2',
    ];
}