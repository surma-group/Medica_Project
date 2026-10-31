<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_type',
        'parent_id',
        'child_type',
        'child_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'parent_type' => 'integer',
        'parent_id'   => 'integer',
        'child_type'  => 'integer',
        'child_id'    => 'integer',
        'status'      => 'boolean',
        'created_by'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Creator
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Parent Entity
    |--------------------------------------------------------------------------
    |
    | USER_TYPE_EMPLOYEE = 5
    | USER_TYPE_DEALER   = 2
    | USER_TYPE_AGENT    = 3
    |
    */

    public function getParentEntityAttribute()
    {
        return $this->resolveEntity(
            $this->parent_type,
            $this->parent_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Child Entity
    |--------------------------------------------------------------------------
    */

    public function getChildEntityAttribute()
    {
        return $this->resolveEntity(
            $this->child_type,
            $this->child_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Entity
    |--------------------------------------------------------------------------
    */

    protected function resolveEntity($type, $id)
    {
        if (!$id) {
            return null;
        }

        if ((int) $type === USER_TYPE_EMPLOYEE) {
            return Employee::find($id);
        }

        if (
            (int) $type === USER_TYPE_DEALER ||
            (int) $type === USER_TYPE_AGENT
        ) {
            return Partner::find($id);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Parent Name
    |--------------------------------------------------------------------------
    */

    public function getParentNameAttribute()
    {
        $parent = $this->parent_entity;

        if (!$parent) {
            return '';
        }

        if ((int) $this->parent_type === USER_TYPE_EMPLOYEE) {
            return $parent->full_name;
        }

        return $parent->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Child Name
    |--------------------------------------------------------------------------
    */

    public function getChildNameAttribute()
    {
        $child = $this->child_entity;

        if (!$child) {
            return '';
        }

        if ((int) $this->child_type === USER_TYPE_EMPLOYEE) {
            return $child->full_name;
        }

        return $child->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Parent Type Name
    |--------------------------------------------------------------------------
    */

    public function getParentTypeNameAttribute()
    {
        return $this->getStakeholderTypeName(
            $this->parent_type
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Child Type Name
    |--------------------------------------------------------------------------
    */

    public function getChildTypeNameAttribute()
    {
        return $this->getStakeholderTypeName(
            $this->child_type
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Stakeholder Type Name
    |--------------------------------------------------------------------------
    */

    protected function getStakeholderTypeName($type)
    {
        return match ((int) $type) {
            USER_TYPE_CUSTOMER => 'Customer',
            USER_TYPE_DEALER   => 'Dealer',
            USER_TYPE_AGENT    => 'Agent',
            USER_TYPE_SUPPLIER => 'Supplier',
            USER_TYPE_EMPLOYEE => 'Employee',
            default            => 'Unknown',
        };
    }
}
