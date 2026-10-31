<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingAreaScope extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'scope_type',
        'scope_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Parent marketing area assignment.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(
            MarketingAreaAssignment::class,
            'assignment_id'
        );
    }

    /**
     * User who created this scope.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * User who last updated this scope.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}
