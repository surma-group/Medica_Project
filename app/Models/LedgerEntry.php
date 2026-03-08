<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $table = 'ledger_entry';
    protected $fillable = ['code', 'voucher_id', 'ledger_id', 'debit', 'credit', 'note'];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }

    public function voucher()
    {
        return $this->belongsTo(VoucherEntry::class, 'voucher_id');
    }
}
