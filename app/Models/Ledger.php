<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $table = 'ledger';
    protected $fillable = [
        'chart_account_id',
        'title',
        'code',
        'type',
        'for_income',
        'for_expense',
        'reference_id'
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'ledger_id');
    }

    public function currentEmployeeBalance()
    {
        return round($this->entries()->sum('credit') - $this->entries()->sum('debit'), 2);
    }
}
