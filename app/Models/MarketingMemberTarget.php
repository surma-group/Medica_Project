<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingMemberTarget extends Model
{
    protected $table = 'marketing_member_targets';

    protected $fillable = [
        'member_type',
        'member_id',
        'period_type',
        'target_type',
        'target_amount',
    ];

    protected $casts = [
        'member_type' => 'integer',
        'member_id' => 'integer',
        'period_type' => 'integer',
        'target_type' => 'integer',
        'target_amount' => 'decimal:2',
    ];
}