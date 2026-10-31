<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Partner;
use App\Models\Designation;
use App\Models\MarketingRelation;
use Illuminate\Http\Request;

class MarketingChainController extends Controller
{

    public function index()
    {

        $employees = Employee::with('designation')
            ->where('status', 1)
            ->orderBy('full_name')
            ->get();

        $partners = Partner::whereIn('type', [
                USER_TYPE_DEALER,
                USER_TYPE_AGENT,
            ])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $dealers = $partners
            ->where('type', USER_TYPE_DEALER)
            ->values();

        $agents = $partners
            ->where('type', USER_TYPE_AGENT)
            ->values();


        $designations = Designation::with('parent')
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        $relations = MarketingRelation::where('status', 1)
            ->orderBy('id')
            ->get();


        $chain = $this->buildChain(
            $relations,
            $employees,
            $partners
        );


        $employeeData = [];

        foreach ($employees as $employee) {
            $employeeData[] = [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'employee_code' => $employee->employee_code,
                'designation_id' => $employee->designation_id,
                'designation' => optional(
                    $employee->designation
                )->name,

                // Existing stakeholder type
                'type' => USER_TYPE_EMPLOYEE,
            ];
        }

        $dealerData = [];

        foreach ($dealers as $dealer) {
            $dealerData[] = [
                'id' => $dealer->id,
                'name' => $dealer->name,
                'shop_name' => $dealer->shop_name,

                // Actual stakeholder type
                'type' => USER_TYPE_DEALER,

                'designation' => 'Dealer',
            ];
        }

        $agentData = [];

        foreach ($agents as $agent) {
            $agentData[] = [
                'id' => $agent->id,
                'name' => $agent->name,
                'shop_name' => $agent->shop_name,

                // Actual stakeholder type
                'type' => USER_TYPE_AGENT,

                'designation' => 'Agent',
            ];
        }

        $partnerData = [];

        foreach ($partners as $partner) {
            $partnerType = (int) $partner->type;

            $partnerData[] = [
                'id' => $partner->id,
                'name' => $partner->name,
                'shop_name' => $partner->shop_name,

                'type' => $partnerType,

                'designation' =>
                    $partnerType === USER_TYPE_DEALER
                        ? 'Dealer'
                        : 'Agent',
            ];
        }


        $designationData = [];

        foreach ($designations as $designation) {
            $designationData[] = [
                'id' => $designation->id,
                'name' => $designation->name,
                'parent_id' => $designation->parent_id,
            ];
        }


        $relationData = [];

        foreach ($relations as $relation) {

            $parent = $this->findEntity(
                $relation->parent_type,
                $relation->parent_id
            );

            $child = $this->findEntity(
                $relation->child_type,
                $relation->child_id
            );

            $relationData[] = [
                'id' => $relation->id,

                'parent_type' => (int) $relation->parent_type,
                'parent_id' => (int) $relation->parent_id,

                'parent_name' => $this->getEntityName(
                    $relation->parent_type,
                    $parent
                ),

                'parent_entity_type' => $this->getEntityTypeName(
                    $relation->parent_type,
                    $parent
                ),

                'child_type' => (int) $relation->child_type,
                'child_id' => (int) $relation->child_id,

                'child_name' => $this->getEntityName(
                    $relation->child_type,
                    $child
                ),

                'child_entity_type' => $this->getEntityTypeName(
                    $relation->child_type,
                    $child
                ),

                'status' => (int) $relation->status,
            ];
        }

        return view(
            'admin.marketing.chain',
            compact(
                'employees',
                'partners',
                'dealers',
                'agents',
                'designations',
                'relations',
                'chain',
                'employeeData',
                'partnerData',
                'dealerData',
                'agentData',
                'designationData',
                'relationData'
            )
        );
    }

    private function buildChain(
        $relations,
        $employees,
        $partners
    ) {
        $entityMap = [];


        foreach ($employees as $employee) {

            $key =
                USER_TYPE_EMPLOYEE .
                ':' .
                $employee->id;

            $entityMap[$key] = [
                'type' => USER_TYPE_EMPLOYEE,
                'id' => $employee->id,
                'name' => $employee->full_name,
                'employee_code' => $employee->employee_code,
                'designation' => optional(
                    $employee->designation
                )->name,
                'designation_id' => $employee->designation_id,
                'partner_type' => null,
            ];
        }


        foreach ($partners as $partner) {

            $partnerType = (int) $partner->type;

            if (
                !in_array(
                    $partnerType,
                    [
                        USER_TYPE_DEALER,
                        USER_TYPE_AGENT,
                    ],
                    true
                )
            ) {
                continue;
            }

            $key =
                $partnerType .
                ':' .
                $partner->id;

            $entityMap[$key] = [
                'type' => $partnerType,
                'id' => $partner->id,
                'name' => $partner->name,
                'employee_code' => null,
                'designation' =>
                    $partnerType === USER_TYPE_DEALER
                        ? 'Dealer'
                        : 'Agent',
                'designation_id' => null,
                'partner_type' => $partnerType,
            ];
        }

        $relationsByParent = $relations->groupBy(
            function ($relation) {
                return
                    (int) $relation->parent_type .
                    ':' .
                    (int) $relation->parent_id;
            }
        );

        $parentKeys = $relations
            ->map(function ($relation) {
                return
                    (int) $relation->parent_type .
                    ':' .
                    (int) $relation->parent_id;
            })
            ->unique()
            ->values();


        $childKeys = $relations
            ->map(function ($relation) {
                return
                    (int) $relation->child_type .
                    ':' .
                    (int) $relation->child_id;
            })
            ->unique()
            ->values();

        $rootKeys = $parentKeys
            ->diff($childKeys)
            ->values();

        $chain = [];


        foreach ($rootKeys as $rootKey) {

            if (!isset($entityMap[$rootKey])) {
                continue;
            }

            $chain[] = $this->buildEntityTree(
                $rootKey,
                $entityMap[$rootKey],
                $relationsByParent,
                $entityMap,
                []
            );
        }

        foreach ($parentKeys as $parentKey) {

            if (
                !isset($entityMap[$parentKey]) ||
                $rootKeys->contains($parentKey)
            ) {
                continue;
            }

            $alreadyExists = false;

            foreach ($chain as $root) {

                if (
                    $this->treeContainsEntity(
                        $root,
                        $parentKey
                    )
                ) {
                    $alreadyExists = true;
                    break;
                }
            }

            if ($alreadyExists) {
                continue;
            }

            $chain[] = $this->buildEntityTree(
                $parentKey,
                $entityMap[$parentKey],
                $relationsByParent,
                $entityMap,
                []
            );
        }

        return $chain;
    }

    private function treeContainsEntity(
        $node,
        $entityKey
    ) {
        $currentKey =
            (int) $node['type'] .
            ':' .
            $node['id'];

        if ($currentKey === $entityKey) {
            return true;
        }

        foreach (
            $node['children'] ?? []
            as $child
        ) {
            if (
                $this->treeContainsEntity(
                    $child,
                    $entityKey
                )
            ) {
                return true;
            }
        }

        return false;
    }

    private function buildEntityTree(
        $entityKey,
        $entity,
        $relationsByParent,
        $entityMap,
        $visited
    ) {
        if (
            in_array(
                $entityKey,
                $visited,
                true
            )
        ) {
            return [
                'type' => (int) $entity['type'],
                'id' => $entity['id'],
                'name' => $entity['name'],
                'employee_code' =>
                    $entity['employee_code'] ?? null,
                'designation' =>
                    $entity['designation'] ?? null,
                'designation_id' =>
                    $entity['designation_id'] ?? null,
                'partner_type' =>
                    $entity['partner_type'] ?? null,

                'relation_id' => null,
                'parent_type' => null,
                'parent_id' => null,

                'children' => [],
            ];
        }

        $visited[] = $entityKey;

        $children = [];

        $relations =
            $relationsByParent->get(
                $entityKey,
                collect()
            );

        foreach ($relations as $relation) {

            $childKey =
                (int) $relation->child_type .
                ':' .
                (int) $relation->child_id;

            if (
                !isset(
                    $entityMap[$childKey]
                )
            ) {
                continue;
            }

            $child =
                $this->buildEntityTree(
                    $childKey,
                    $entityMap[$childKey],
                    $relationsByParent,
                    $entityMap,
                    $visited
                );

            $child['relation_id'] =
                $relation->id;

            $child['parent_type'] =
                (int) $relation->parent_type;

            $child['parent_id'] =
                (int) $relation->parent_id;

            $children[] = $child;
        }

        return [
            'type' => (int) $entity['type'],
            'id' => $entity['id'],
            'name' => $entity['name'],
            'employee_code' =>
                $entity['employee_code'] ?? null,
            'designation' =>
                $entity['designation'] ?? null,
            'designation_id' =>
                $entity['designation_id'] ?? null,
            'partner_type' =>
                $entity['partner_type'] ?? null,

            'relation_id' => null,
            'parent_type' => null,
            'parent_id' => null,

            'children' => $children,
        ];
    }

    public function store(
        Request $request
    ) {
        $validated = $request->validate([
            'parent_type' => [
                'required',
                'integer',
                'in:' .
                    USER_TYPE_DEALER . ',' .
                    USER_TYPE_AGENT . ',' .
                    USER_TYPE_EMPLOYEE,
            ],

            'parent_id' => [
                'required',
                'integer',
            ],

            'child_type' => [
                'required',
                'integer',
                'in:' .
                    USER_TYPE_DEALER . ',' .
                    USER_TYPE_AGENT . ',' .
                    USER_TYPE_EMPLOYEE,
            ],

            'child_id' => [
                'required',
                'integer',
            ],
        ]);

        $parentType = (int) $validated['parent_type'];
        $parentId = (int) $validated['parent_id'];

        $childType = (int) $validated['child_type'];
        $childId = (int) $validated['child_id'];


        if (
            $parentType === $childType &&
            $parentId === $childId
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'An entity cannot be assigned to itself.',
            ], 422);
        }



        $parent = $this->findEntity(
            $parentType,
            $parentId
        );

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Parent entity not found or inactive.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Find Child
        |--------------------------------------------------------------------------
        */

        $child = $this->findEntity(
            $childType,
            $childId
        );

        if (!$child) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Child entity not found or inactive.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Business Rule
        |--------------------------------------------------------------------------
        */

        $validation =
            $this->validateMarketingRelation(
                $parentType,
                $parent,
                $childType,
                $child
            );

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' =>
                    $validation['message'],
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Existing Relation
        |--------------------------------------------------------------------------
        */

        $relation =
            MarketingRelation::where(
                'parent_type',
                $parentType
            )
            ->where(
                'parent_id',
                $parentId
            )
            ->where(
                'child_type',
                $childType
            )
            ->where(
                'child_id',
                $childId
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Reactivate Existing Relation
        |--------------------------------------------------------------------------
        */

        if ($relation) {

            if ($relation->status == 1) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'This marketing relation is already active.',
                    'relation_id' =>
                        $relation->id,
                ], 422);
            }

            $relation->update([
                'status' => 1,
                'created_by' => auth()->id(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create New Relation
        |--------------------------------------------------------------------------
        */

        else {

            $relation =
                MarketingRelation::create([
                    'parent_type' => $parentType,
                    'parent_id' => $parentId,

                    'child_type' => $childType,
                    'child_id' => $childId,

                    'status' => 1,

                    'created_by' =>
                        auth()->id(),
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | JSON Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'Marketing relation added successfully.',

            'relation_id' =>
                $relation->id,

            'parent_type' =>
                (int) $relation->parent_type,

            'parent_id' =>
                (int) $relation->parent_id,

            'parent_name' =>
                $this->getEntityName(
                    $parentType,
                    $parent
                ),

            'parent_entity_type' =>
                $this->getEntityTypeName(
                    $parentType,
                    $parent
                ),

            'child_type' =>
                (int) $relation->child_type,

            'child_id' =>
                (int) $relation->child_id,

            'child_name' =>
                $this->getEntityName(
                    $childType,
                    $child
                ),

            'child_entity_type' =>
                $this->getEntityTypeName(
                    $childType,
                    $child
                ),

            'status' =>
                (int) $relation->status,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Relation
    |--------------------------------------------------------------------------
    */

    public function cancel(
        MarketingRelation $relation
    ) {
        if ($relation->status == 0) {
            return response()->json([
                'success' => false,
                'message' =>
                    'This marketing relation is already cancelled.',
            ], 422);
        }

        $relation->update([
            'status' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Marketing relation cancelled successfully.',
            'relation_id' =>
                $relation->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Find Entity
    |--------------------------------------------------------------------------
    */

    private function findEntity(
        int $type,
        int $id
    ) {
        /*
        |--------------------------------------------------------------------------
        | Employee
        |--------------------------------------------------------------------------
        */

        if ($type === USER_TYPE_EMPLOYEE) {

            return Employee::with('designation')
                ->where('status', 1)
                ->find($id);
        }

        /*
        |--------------------------------------------------------------------------
        | Dealer / Agent
        |--------------------------------------------------------------------------
        */

        if (
            $type === USER_TYPE_DEALER ||
            $type === USER_TYPE_AGENT
        ) {

            return Partner::where(
                    'type',
                    $type
                )
                ->where('status', 1)
                ->find($id);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Entity Name
    |--------------------------------------------------------------------------
    */

    private function getEntityName(
        int $type,
        $entity
    ) {
        if (!$entity) {
            return 'Unknown';
        }

        if ($type === USER_TYPE_EMPLOYEE) {
            return $entity->full_name ?? 'Unknown';
        }

        return $entity->name ?? 'Unknown';
    }

    /*
    |--------------------------------------------------------------------------
    | Entity Type Name
    |--------------------------------------------------------------------------
    */

    private function getEntityTypeName(
        int $type,
        $entity = null
    ) {
        return match ($type) {

            USER_TYPE_EMPLOYEE =>
                'Employee',

            USER_TYPE_DEALER =>
                'Dealer',

            USER_TYPE_AGENT =>
                'Agent',

            USER_TYPE_CUSTOMER =>
                'Customer',

            USER_TYPE_SUPPLIER =>
                'Supplier',

            default =>
                'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Marketing Relation
    |--------------------------------------------------------------------------
    */

    private function validateMarketingRelation(
        int $parentType,
        $parent,
        int $childType,
        $child
    ) {
        /*
        |--------------------------------------------------------------------------
        | Employee → Employee
        |--------------------------------------------------------------------------
        |
        | Must follow designation hierarchy.
        |
        */

        if (
            $parentType === USER_TYPE_EMPLOYEE &&
            $childType === USER_TYPE_EMPLOYEE
        ) {

            if (!$parent->designation_id) {
                return [
                    'valid' => false,
                    'message' =>
                        'The selected employee does not have a designation.',
                ];
            }

            if (!$child->designation_id) {
                return [
                    'valid' => false,
                    'message' =>
                        'The selected child employee does not have a designation.',
                ];
            }

            $valid =
                Designation::where(
                    'id',
                    $child->designation_id
                )
                ->where(
                    'parent_id',
                    $parent->designation_id
                )
                ->where(
                    'status',
                    1
                )
                ->exists();

            if (!$valid) {

                $parentDesignation =
                    optional(
                        $parent->designation
                    )->name ??
                    'selected employee';

                $childDesignation =
                    optional(
                        $child->designation
                    )->name ??
                    'selected employee';

                return [
                    'valid' => false,
                    'message' =>
                        $childDesignation .
                        ' cannot be assigned under ' .
                        $parentDesignation .
                        '. Please follow the designation hierarchy.',
                ];
            }

            return [
                'valid' => true,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Employee → Dealer
        |--------------------------------------------------------------------------
        |
        | Only Sales Executive can have Dealer.
        |
        */

        if (
            $parentType === USER_TYPE_EMPLOYEE &&
            $childType === USER_TYPE_DEALER
        ) {

            if (
                (int) $child->type !==
                USER_TYPE_DEALER
            ) {
                return [
                    'valid' => false,
                    'message' =>
                        'Only a Dealer can be assigned under an employee.',
                ];
            }

            if (!$parent->designation) {
                return [
                    'valid' => false,
                    'message' =>
                        'The selected employee does not have a designation.',
                ];
            }

            $designationName =
                strtoupper(
                    trim(
                        $parent->designation->name
                    )
                );

            if (
                $designationName !==
                'SALES EXECUTIVE'
            ) {
                return [
                    'valid' => false,
                    'message' =>
                        'Only Sales Executive can have a Dealer.',
                ];
            }

            return [
                'valid' => true,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Dealer → Agent
        |--------------------------------------------------------------------------
        */

        if (
            $parentType === USER_TYPE_DEALER &&
            $childType === USER_TYPE_AGENT
        ) {

            if (
                (int) $parent->type !==
                USER_TYPE_DEALER
            ) {
                return [
                    'valid' => false,
                    'message' =>
                        'Only a Dealer can have Agents.',
                ];
            }

            if (
                (int) $child->type !==
                USER_TYPE_AGENT
            ) {
                return [
                    'valid' => false,
                    'message' =>
                        'Only an Agent can be assigned under a Dealer.',
                ];
            }

            return [
                'valid' => true,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | All Other Combinations Are Not Allowed
        |--------------------------------------------------------------------------
        */

        return [
            'valid' => false,
            'message' =>
                'This marketing relation is not allowed.',
        ];
    }
}
