<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoLedger extends Model
{
    protected $table = 'auto_ledger';
    protected $fillable = ['auto_charts_accounts_id', 'title', 'code'];
}

