<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingAreaAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_type',
        'member_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Geographic scopes assigned to this member.
     */
    public function scopes(): HasMany
    {
        return $this->hasMany(
            MarketingAreaScope::class,
            'assignment_id'
        );
    }

    /**
     * User who created this assignment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * User who last updated this assignment.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}
