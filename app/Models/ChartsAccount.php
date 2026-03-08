<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartsAccount extends Model
{
    protected $table = 'charts_accounts';
    protected $fillable = ['title', 'code', 'auto_charts_accounts_id', 'master_account_id'];

    public function ledgers()
    {
        return $this->hasMany(Ledger::class, 'chart_account_id');
    }
}

