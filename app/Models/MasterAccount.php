<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAccount extends Model
{
    protected $table = 'master_account';
    protected $fillable = ['title', 'code', 'auto_master_account_id', 'type'];

    public function charts()
    {
        return $this->hasMany(ChartsAccount::class);
    }
}

