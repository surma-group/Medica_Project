<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherEntry extends Model
{
    protected $table = 'voucher_entry';
    protected $fillable = ['code', 'type', 'reference_id', 'created_by'];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
}

