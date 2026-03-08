<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoMasterAccount extends Model
{
    protected $table = 'auto_master_account';
    protected $fillable = ['title', 'code', 'type'];

    public function charts()
    {
        return $this->hasMany(AutoChartsAccount::class);
    }
}
