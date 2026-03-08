<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'mobile',
        'email',
        'address',
        'contact_person',
        'contact_person_mobile',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($supplier) {
            $supplier->code = 'SUP-' . str_pad(
                (self::max('id') + 1),
                6,
                '0',
                STR_PAD_LEFT
            );
        });
    }
}