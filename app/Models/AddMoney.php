<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AddMoney extends Model
{
    use HasFactory;

    protected $table = 'add_money'; // table name

    // Fillable fields for mass assignment
    protected $fillable = [
        'amount',
        'note',
        'created_by',
        'updated_by',
    ];

    /**
     * Boot method to automatically set created_by and updated_by.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Relationship to the user who created the entry
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to the user who last updated the entry
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
