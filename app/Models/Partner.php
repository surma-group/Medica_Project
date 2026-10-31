<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends Model
{
    use HasFactory;

    /**
     * Automatically generate account number.
     */
    protected static function booted(): void
    {
        static::creating(function ($partner) {
            if (empty($partner->account_number)) {
                do {
                    $accountNumber = now()->format('Ymd')
                        . str_pad(
                            (string) random_int(0, 9999),
                            4,
                            '0',
                            STR_PAD_LEFT
                        );
                } while (
                    static::where(
                        'account_number',
                        $accountNumber
                    )->exists()
                );

                $partner->account_number = $accountNumber;
            }
        });
    }

    protected $fillable = [
        'user_id',
        'ledger_id',
        'account_number',

        'name',
        'shop_name',
        'type',

        'phone',
        'emergency_contact',
        'email',

        'address',
        'district_id',
        'postcode',

        'image',
        'nid_file',

        'trade_license',
        'trade_license_file',

        'status',
    ];


    /**
     * User Account
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * District
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Wallet Ledger
     */
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Withdraw Requests
     */
    public function withdrawRequests()
    {
        return $this->morphMany(
            WithdrawRequest::class,
            'reference'
        );
    }

    /**
     * Add Money Requests
     */
    public function addMoneyRequests()
    {
        return $this->hasMany(
            AddMoneyRequest::class,
            'user_id',
            'user_id'
        );
    }

    /**
     * Beneficiaries
     */
    public function beneficiaries()
    {
        return $this->hasMany(
            Beneficiary::class,
            'user_id',
            'user_id'
        );
    }


    /**
     * Partner Type Name
     */
    public function getTypeNameAttribute()
    {
        return match ((int) $this->type) {
            1 => 'Dealer',
            2 => 'Agent',
            default => 'Unknown',
        };
    }

    /**
     * Current Wallet Balance
     */
    public function getBalanceAttribute()
    {
        if (!$this->ledger) {
            return 0;
        }

        return $this->ledger->entries()->sum('credit')
            - $this->ledger->entries()->sum('debit');
    }
}
