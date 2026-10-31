<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Partner;
use App\Models\MarketingRelation;
use Illuminate\Http\Request;

class EmployeeMarketingChainController extends Controller
{
    /**
     * Display logged-in employee's marketing chain.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Logged In Employee
        |--------------------------------------------------------------------------
        */
        $employee = $this->getLoggedInEmployee();

        if (!$employee) {
            abort(403, 'Employee account not found.');
        }


        /*
        |--------------------------------------------------------------------------
        | Active Employees
        |--------------------------------------------------------------------------
        */
        $employees = Employee::with('designation')
            ->where('status', 1)
            ->orderBy('full_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Active Dealers / Agents
        |--------------------------------------------------------------------------
        */
        $partners = Partner::whereIn('type', [
                USER_TYPE_DEALER,
                USER_TYPE_AGENT,
            ])
            ->where('status', 1)
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Separate Dealer / Agent
        |--------------------------------------------------------------------------
        */
        $dealers = $partners
            ->where('type', USER_TYPE_DEALER)
            ->values();

        $agents = $partners
            ->where('type', USER_TYPE_AGENT)
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Active Marketing Relations
        |--------------------------------------------------------------------------
        */
        $relations = MarketingRelation::where('status', 1)
            ->orderBy('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Build Entity Map
        |--------------------------------------------------------------------------
        |
        | Same concept as Admin Marketing Chain Controller.
        |
        */
        $entityMap = $this->buildEntityMap(
            $employees,
            $partners
        );


        /*
        |--------------------------------------------------------------------------
        | Relations Grouped By Parent
        |--------------------------------------------------------------------------
        */
        $relationsByParent = $relations->groupBy(
            function ($relation) {
                return
                    (int) $relation->parent_type .
                    ':' .
                    (int) $relation->parent_id;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Relations Grouped By Child
        |--------------------------------------------------------------------------
        */
        $relationsByChild = $relations->groupBy(
            function ($relation) {
                return
                    (int) $relation->child_type .
                    ':' .
                    (int) $relation->child_id;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Current Employee Key
        |--------------------------------------------------------------------------
        */
        $employeeKey =
            USER_TYPE_EMPLOYEE .
            ':' .
            $employee->id;


        /*
        |--------------------------------------------------------------------------
        | Find Root
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | Chairman
        |    ↓
        | Managing Director
        |    ↓
        | General Manager
        |    ↓
        | Sales Executive
        |    ↓
        | Current Employee
        |
        | We move upward until no parent is found.
        |
        */
        $rootKey = $this->findRootKey(
            $employeeKey,
            $relationsByChild,
            $entityMap
        );


        /*
        |--------------------------------------------------------------------------
        | Build Full Marketing Chain
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | $chain must be an ARRAY of root nodes.
        |
        | This matches the Admin Controller structure.
        |
        */
        $chain = [];

        if (
            $rootKey &&
            isset($entityMap[$rootKey])
        ) {
            $chain[] = $this->buildEntityTree(
                $rootKey,
                $entityMap[$rootKey],
                $relationsByParent,
                $entityMap,
                [],
                $employeeKey
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Employee Immediate Parent
        |--------------------------------------------------------------------------
        */
        $parent = null;

        $parentRelation = $relationsByChild
            ->get($employeeKey, collect())
            ->first();

        if ($parentRelation) {

            $parentKey =
                (int) $parentRelation->parent_type .
                ':' .
                (int) $parentRelation->parent_id;

            if (isset($entityMap[$parentKey])) {

                $parent = $entityMap[$parentKey];

                $parent['relation_id'] =
                    $parentRelation->id;

                $parent['parent_type'] =
                    (int) $parentRelation->parent_type;

                $parent['parent_id'] =
                    (int) $parentRelation->parent_id;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Direct Downline
        |--------------------------------------------------------------------------
        */
        $downline = [];

        $employeeChildren = $relationsByParent
            ->get($employeeKey, collect());

        foreach ($employeeChildren as $relation) {

            $childKey =
                (int) $relation->child_type .
                ':' .
                (int) $relation->child_id;

            if (!isset($entityMap[$childKey])) {
                continue;
            }

            $child = $this->buildEntityTree(
                $childKey,
                $entityMap[$childKey],
                $relationsByParent,
                $entityMap,
                [$employeeKey],
                $employeeKey
            );

            $child['relation_id'] =
                $relation->id;

            $child['parent_type'] =
                (int) $relation->parent_type;

            $child['parent_id'] =
                (int) $relation->parent_id;

            $downline[] = $child;
        }


        /*
        |--------------------------------------------------------------------------
        | Employee Data
        |--------------------------------------------------------------------------
        */
        $employeeData = [];

        foreach ($employees as $item) {

            $employeeData[] = [
                'id' => (int) $item->id,

                'name' =>
                    $item->full_name,

                'employee_code' =>
                    $item->employee_code,

                'designation_id' =>
                    $item->designation_id,

                'designation' =>
                    optional(
                        $item->designation
                    )->name,

                'type' =>
                    USER_TYPE_EMPLOYEE,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Dealer Data
        |--------------------------------------------------------------------------
        */
        $dealerData = [];

        foreach ($dealers as $dealer) {

            $dealerData[] = [
                'id' =>
                    (int) $dealer->id,

                'name' =>
                    $dealer->name,

                'shop_name' =>
                    $dealer->shop_name,

                'type' =>
                    USER_TYPE_DEALER,

                'designation' =>
                    'Dealer',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Agent Data
        |--------------------------------------------------------------------------
        */
        $agentData = [];

        foreach ($agents as $agent) {

            $agentData[] = [
                'id' =>
                    (int) $agent->id,

                'name' =>
                    $agent->name,

                'shop_name' =>
                    $agent->shop_name,

                'type' =>
                    USER_TYPE_AGENT,

                'designation' =>
                    'Agent',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Relation Data
        |--------------------------------------------------------------------------
        |
        | This is mainly useful if the Blade needs relation information.
        |
        */
        $relationData = [];

        foreach ($relations as $relation) {

            $parentKey =
                (int) $relation->parent_type .
                ':' .
                (int) $relation->parent_id;

            $childKey =
                (int) $relation->child_type .
                ':' .
                (int) $relation->child_id;


            $parentEntity =
                $entityMap[$parentKey] ?? null;

            $childEntity =
                $entityMap[$childKey] ?? null;


            if (
                !$parentEntity ||
                !$childEntity
            ) {
                continue;
            }


            $relationData[] = [

                'id' =>
                    (int) $relation->id,


                'parent_type' =>
                    (int) $relation->parent_type,


                'parent_id' =>
                    (int) $relation->parent_id,


                'parent_name' =>
                    $parentEntity['name'],


                'parent_entity_type' =>
                    $parentEntity['entity_type'],


                'child_type' =>
                    (int) $relation->child_type,


                'child_id' =>
                    (int) $relation->child_id,


                'child_name' =>
                    $childEntity['name'],


                'child_entity_type' =>
                    $childEntity['entity_type'],


                'status' =>
                    (int) $relation->status,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */
        return view(
            'employee.marketing.chain',
            compact(
                'employee',
                'parent',
                'downline',
                'chain',
                'relations',
                'relationData',
                'employeeData',
                'dealerData',
                'agentData'
            )
        );
    }


    /**
     * Get currently logged-in employee.
     */
    private function getLoggedInEmployee()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Case 1:
        | Authenticated model itself is Employee
        |--------------------------------------------------------------------------
        */
        if ($user instanceof Employee) {

            return $user->load(
                'designation'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Case 2:
        | Auth User -> employee relation
        |--------------------------------------------------------------------------
        |
        | Your existing employee Blade already uses:
        |
        | Auth::user()->employee
        |
        */
        if (
            method_exists(
                $user,
                'employee'
            )
        ) {

            $employee = $user->employee;

            if ($employee) {

                return $employee->load(
                    'designation'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Case 3:
        | Auth User has employee_id
        |--------------------------------------------------------------------------
        */
        if (
            isset($user->employee_id) &&
            $user->employee_id
        ) {

            return Employee::with(
                    'designation'
                )
                ->where(
                    'status',
                    1
                )
                ->find(
                    $user->employee_id
                );
        }


        return null;
    }


    /**
     * Build all marketing entities.
     */
    private function buildEntityMap(
        $employees,
        $partners
    ) {
        $entityMap = [];


        /*
        |--------------------------------------------------------------------------
        | Employees
        |--------------------------------------------------------------------------
        */
        foreach ($employees as $employee) {

            $key =
                USER_TYPE_EMPLOYEE .
                ':' .
                $employee->id;


            $entityMap[$key] = [

                'type' =>
                    USER_TYPE_EMPLOYEE,

                'id' =>
                    (int) $employee->id,

                'name' =>
                    $employee->full_name,

                'employee_code' =>
                    $employee->employee_code,

                'designation' =>
                    optional(
                        $employee->designation
                    )->name,

                'designation_id' =>
                    $employee->designation_id,

                'partner_type' =>
                    null,

                'entity_type' =>
                    'Employee',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Dealers / Agents
        |--------------------------------------------------------------------------
        */
        foreach ($partners as $partner) {

            $partnerType =
                (int) $partner->type;


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

                'type' =>
                    $partnerType,

                'id' =>
                    (int) $partner->id,

                'name' =>
                    $partner->name,

                'employee_code' =>
                    null,

                'designation' =>
                    $partnerType ===
                    USER_TYPE_DEALER
                        ? 'Dealer'
                        : 'Agent',

                'designation_id' =>
                    null,

                'partner_type' =>
                    $partnerType,

                'entity_type' =>
                    $partnerType ===
                    USER_TYPE_DEALER
                        ? 'Dealer'
                        : 'Agent',
            ];
        }


        return $entityMap;
    }


    /**
     * Find top-most/root entity.
     */
    private function findRootKey(
        $currentKey,
        $relationsByChild,
        $entityMap
    ) {
        $visited = [];


        while ($currentKey) {

            /*
            |--------------------------------------------------------------------------
            | Circular protection
            |--------------------------------------------------------------------------
            */
            if (
                in_array(
                    $currentKey,
                    $visited,
                    true
                )
            ) {
                break;
            }


            $visited[] =
                $currentKey;


            /*
            |--------------------------------------------------------------------------
            | Find current entity's parent
            |--------------------------------------------------------------------------
            */
            $relation =
                $relationsByChild
                    ->get(
                        $currentKey,
                        collect()
                    )
                    ->first();


            /*
            |--------------------------------------------------------------------------
            | No parent = root
            |--------------------------------------------------------------------------
            */
            if (!$relation) {
                break;
            }


            $parentKey =
                (int) $relation->parent_type .
                ':' .
                (int) $relation->parent_id;


            /*
            |--------------------------------------------------------------------------
            | Parent entity must exist
            |--------------------------------------------------------------------------
            */
            if (
                !isset(
                    $entityMap[$parentKey]
                )
            ) {
                break;
            }


            $currentKey =
                $parentKey;
        }


        return $currentKey;
    }


    /**
     * Build recursive marketing tree.
     */
    private function buildEntityTree(
        $entityKey,
        $entity,
        $relationsByParent,
        $entityMap,
        $visited = [],
        $currentEmployeeKey = null
    ) {
        /*
        |--------------------------------------------------------------------------
        | Circular Protection
        |--------------------------------------------------------------------------
        */
        if (
            in_array(
                $entityKey,
                $visited,
                true
            )
        ) {

            return [

                'type' =>
                    (int) $entity['type'],

                'id' =>
                    (int) $entity['id'],

                'name' =>
                    $entity['name'],

                'employee_code' =>
                    $entity['employee_code'] ?? null,

                'designation' =>
                    $entity['designation'] ?? null,

                'designation_id' =>
                    $entity['designation_id'] ?? null,

                'partner_type' =>
                    $entity['partner_type'] ?? null,

                'entity_type' =>
                    $entity['entity_type'] ?? 'Unknown',

                'is_current_employee' =>
                    $entityKey ===
                    $currentEmployeeKey,

                'relation_id' =>
                    null,

                'parent_type' =>
                    null,

                'parent_id' =>
                    null,

                'children' =>
                    [],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Add Current Entity To Visited
        |--------------------------------------------------------------------------
        */
        $visited[] =
            $entityKey;


        $children = [];


        /*
        |--------------------------------------------------------------------------
        | Get Children
        |--------------------------------------------------------------------------
        */
        $childRelations =
            $relationsByParent->get(
                $entityKey,
                collect()
            );


        foreach (
            $childRelations as $relation
        ) {

            $childKey =
                (int) $relation->child_type .
                ':' .
                (int) $relation->child_id;


            /*
            |--------------------------------------------------------------------------
            | Child entity does not exist
            |--------------------------------------------------------------------------
            */
            if (
                !isset(
                    $entityMap[$childKey]
                )
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Circular relation protection
            |--------------------------------------------------------------------------
            */
            if (
                in_array(
                    $childKey,
                    $visited,
                    true
                )
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Build Child
            |--------------------------------------------------------------------------
            */
            $child =
                $this->buildEntityTree(
                    $childKey,
                    $entityMap[$childKey],
                    $relationsByParent,
                    $entityMap,
                    $visited,
                    $currentEmployeeKey
                );


            /*
            |--------------------------------------------------------------------------
            | Relation Information
            |--------------------------------------------------------------------------
            */
            $child['relation_id'] =
                (int) $relation->id;


            $child['parent_type'] =
                (int) $relation->parent_type;


            $child['parent_id'] =
                (int) $relation->parent_id;


            $children[] =
                $child;
        }


        /*
        |--------------------------------------------------------------------------
        | Return Node
        |--------------------------------------------------------------------------
        */
        return [

            'type' =>
                (int) $entity['type'],

            'id' =>
                (int) $entity['id'],

            'name' =>
                $entity['name'],

            'employee_code' =>
                $entity['employee_code'] ?? null,

            'designation' =>
                $entity['designation'] ?? null,

            'designation_id' =>
                $entity['designation_id'] ?? null,

            'partner_type' =>
                $entity['partner_type'] ?? null,

            'entity_type' =>
                $entity['entity_type'] ?? 'Unknown',

            'is_current_employee' =>
                $entityKey ===
                $currentEmployeeKey,

            'relation_id' =>
                null,

            'parent_type' =>
                null,

            'parent_id' =>
                null,

            'children' =>
                $children,
        ];
    }
}