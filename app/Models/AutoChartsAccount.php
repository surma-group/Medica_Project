<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoChartsAccount extends Model
{
    protected $table = 'auto_charts_accounts';
    protected $fillable = ['auto_master_account_id', 'title', 'code'];

    public function ledgers()
    {
        return $this->hasMany(AutoLedger::class);
    }
}

