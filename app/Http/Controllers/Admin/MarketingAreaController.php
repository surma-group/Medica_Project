<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use App\Models\Employee;
use App\Models\Partner;
use App\Models\MarketingAreaAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MarketingAreaController extends Controller
{
    /**
     * Display Marketing Area page.
     */
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

        $countries = Country::orderBy('name_en')->get();

        return view(
            'admin.marketing.area',
            compact(
                'employees',
                'dealers',
                'agents',
                'countries'
            )
        );
    }

    /**
     * Return divisions for the selected country.
     */
    public function divisions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $divisions = Division::where(
            'country_id',
            $request->country_id
        )
            ->orderBy('name_en')
            ->get([
                'id',
                'country_id',
                'name_en',
                'name_bn',
            ]);

        return response()->json([
            'success' => true,
            'data' => $divisions,
        ]);
    }

    /**
     * Return districts for the selected divisions.
     */
    public function districts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'division_ids' => 'required|array|min:1',
            'division_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:divisions,id',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $districts = District::whereIn(
            'division_id',
            $request->input('division_ids', [])
        )
            ->orderBy('name_en')
            ->get([
                'id',
                'division_id',
                'name_en',
                'name_bn',
            ]);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Return upazilas for the selected districts.
     */
    public function upazilas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'district_ids' => 'required|array|min:1',
            'district_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:districts,id',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $upazilas = Upazila::whereIn(
            'district_id',
            $request->input('district_ids', [])
        )
            ->orderBy('name_en')
            ->get([
                'id',
                'district_id',
                'name_en',
                'name_bn',
            ]);

        return response()->json([
            'success' => true,
            'data' => $upazilas,
        ]);
    }


    /**
     * Return the saved area assignment for a member,
     * including area names and parent IDs for editing.
     */
    public function getAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_type' => [
                'required',
                'integer',
                Rule::in([
                    USER_TYPE_EMPLOYEE,
                    USER_TYPE_DEALER,
                    USER_TYPE_AGENT,
                ]),
            ],
            'member_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $assignment = MarketingAreaAssignment::with('scopes')
            ->where('member_type', $request->member_type)
            ->where('member_id', $request->member_id)
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => true,
                'exists' => false,
                'data' => null,
            ]);
        }

        $countryId = null;
        $scopes = [];

        foreach ($assignment->scopes as $scope) {
            $item = [
                'scope_type' => $scope->scope_type,
                'scope_id' => (int) $scope->scope_id,
                'scope_name' => 'Unknown area',
                'name_en' => null,
                'name_bn' => null,
                'country_id' => null,
                'division_id' => null,
                'district_id' => null,
                'upazila_id' => null,
            ];

            switch ($scope->scope_type) {
                case 'country':
                    $area = Country::find($scope->scope_id);

                    if ($area) {
                        $item['name_en'] = $area->name_en;
                        $item['name_bn'] = $area->name_bn;
                        $item['scope_name'] = $area->name_en
                            ?: ($area->name_bn ?: 'Unknown area');

                        $item['country_id'] = (int) $area->id;
                        $countryId = (int) $area->id;
                    }
                    break;

                case 'division':
                    $area = Division::find($scope->scope_id);

                    if ($area) {
                        $item['name_en'] = $area->name_en;
                        $item['name_bn'] = $area->name_bn;
                        $item['scope_name'] = $area->name_en
                            ?: ($area->name_bn ?: 'Unknown area');

                        $item['country_id'] = (int) $area->country_id;
                        $item['division_id'] = (int) $area->id;
                        $countryId = (int) $area->country_id;
                    }
                    break;

                case 'district':
                    $area = District::find($scope->scope_id);

                    if ($area) {
                        $division = Division::find($area->division_id);

                        $item['name_en'] = $area->name_en;
                        $item['name_bn'] = $area->name_bn;
                        $item['scope_name'] = $area->name_en
                            ?: ($area->name_bn ?: 'Unknown area');

                        $item['division_id'] = (int) $area->division_id;
                        $item['district_id'] = (int) $area->id;

                        if ($division) {
                            $item['country_id'] = (int) $division->country_id;
                            $countryId = (int) $division->country_id;
                        }
                    }
                    break;

                case 'upazila':
                    $area = Upazila::find($scope->scope_id);

                    if ($area) {
                        $district = District::find($area->district_id);
                        $division = $district
                            ? Division::find($district->division_id)
                            : null;

                        $item['name_en'] = $area->name_en;
                        $item['name_bn'] = $area->name_bn;
                        $item['scope_name'] = $area->name_en
                            ?: ($area->name_bn ?: 'Unknown area');

                        $item['upazila_id'] = (int) $area->id;
                        $item['district_id'] = $district
                            ? (int) $district->id
                            : null;
                        $item['division_id'] = $district
                            ? (int) $district->division_id
                            : null;
                        $item['country_id'] = $division
                            ? (int) $division->country_id
                            : null;

                        if ($division) {
                            $countryId = (int) $division->country_id;
                        }
                    }
                    break;
            }

            $scopes[] = $item;
        }

        return response()->json([
            'success' => true,
            'exists' => true,
            'data' => [
                'id' => $assignment->id,
                'country_id' => $countryId,
                'scopes' => $scopes,
            ],
        ]);
    }


    /**
     * Filter active marketing members by type and assigned working area.
     */
    public function filterMembers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_type' => 'nullable|in:all,employee,dealer,agent',
            'division_id' => 'nullable|integer|exists:divisions,id',
            'district_id' => 'nullable|integer|exists:districts,id',
            'upazila_id' => 'nullable|integer|exists:upazilas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $typeMap = [
            'employee' => USER_TYPE_EMPLOYEE,
            'dealer' => USER_TYPE_DEALER,
            'agent' => USER_TYPE_AGENT,
        ];

        $selectedType = $request->input('member_type', 'all');

        $memberTypes = $selectedType === 'all'
            ? array_values($typeMap)
            : [$typeMap[$selectedType]];

        /*
     * Get active members first.
     * When no area is selected, this also allows unassigned members
     * to appear in the result.
     */
        $members = collect();

        if (in_array(USER_TYPE_EMPLOYEE, $memberTypes)) {
            $members = $members->merge(
                Employee::where('status', 1)
                    ->get(['id'])
                    ->map(fn($member) => [
                        'member_type' => USER_TYPE_EMPLOYEE,
                        'member_id' => (int) $member->id,
                    ])
            );
        }

        $partnerTypes = array_values(array_intersect(
            $memberTypes,
            [USER_TYPE_DEALER, USER_TYPE_AGENT]
        ));

        if (!empty($partnerTypes)) {
            $members = $members->merge(
                Partner::where('status', 1)
                    ->whereIn('type', $partnerTypes)
                    ->get(['id', 'type'])
                    ->map(fn($member) => [
                        'member_type' => (int) $member->type,
                        'member_id' => (int) $member->id,
                    ])
            );
        }

        $divisionId = $request->filled('division_id')
            ? (int) $request->division_id
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $upazilaId = $request->filled('upazila_id')
            ? (int) $request->upazila_id
            : null;

        // No area selected: filter by member type only.
        if (!$divisionId && !$districtId && !$upazilaId) {
            return response()->json([
                'success' => true,
                'data' => $members->values(),
            ]);
        }

        /*
     * Resolve the selected area's ancestors.
     */
        if ($upazilaId) {
            $upazila = Upazila::findOrFail($upazilaId);
            $districtId = (int) $upazila->district_id;
        }

        if ($districtId) {
            $district = District::findOrFail($districtId);
            $divisionId = (int) $district->division_id;
        }

        $division = Division::findOrFail($divisionId);
        $countryId = (int) $division->country_id;

        /*
     * Build scope IDs that cover the selected location.
     * A parent-level assignment includes its child locations.
     */
        $districtIds = District::where('division_id', $divisionId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $upazilaQuery = Upazila::whereIn('district_id', $districtIds);

        if ($districtId) {
            $upazilaQuery->where('district_id', $districtId);
        }

        $upazilaIds = $upazilaQuery
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $scopeIds = [
            'country' => [$countryId],
            'division' => [$divisionId],
            'district' => $districtIds,
            'upazila' => $upazilaIds,
        ];

        /*
     * If a more specific location was selected, only scopes covering
     * that location should match.
     */
        if ($districtId) {
            $scopeIds['district'] = [$districtId];
        }

        if ($upazilaId) {
            $scopeIds['upazila'] = [$upazilaId];
        }

        $assignments = MarketingAreaAssignment::whereIn(
            'member_type',
            $memberTypes
        )
            ->whereHas('scopes', function ($query) use ($scopeIds) {
                $query->where(function ($nested) use ($scopeIds) {
                    foreach ($scopeIds as $scopeType => $ids) {
                        if (empty($ids)) {
                            continue;
                        }

                        $nested->orWhere(function ($condition) use (
                            $scopeType,
                            $ids
                        ) {
                            $condition->where('scope_type', $scopeType)
                                ->whereIn('scope_id', $ids);
                        });
                    }
                });
            })
            ->get(['member_type', 'member_id']);

        $matchingKeys = $assignments->mapWithKeys(function ($assignment) {
            return [
                (int) $assignment->member_type . ':' .
                    (int) $assignment->member_id => true,
            ];
        });

        $filteredMembers = $members->filter(function ($member) use ($matchingKeys) {
            $key = $member['member_type'] . ':' . $member['member_id'];

            return $matchingKeys->has($key);
        })->values();

        return response()->json([
            'success' => true,
            'data' => $filteredMembers,
        ]);
    }


    /**
     * Save or update a member's marketing area.
     *
     * Multiple selected levels are saved together:
     * Division + District + Upazila.
     *
     * If no smaller area is selected, the whole country is saved.
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_type' => [
                'required',
                'integer',
                Rule::in([
                    USER_TYPE_EMPLOYEE,
                    USER_TYPE_DEALER,
                    USER_TYPE_AGENT,
                ]),
            ],

            'member_id' => 'required|integer|min:1',

            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'division_ids' => 'sometimes|array',
            'division_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:divisions,id',
            ],

            'district_ids' => 'sometimes|array',
            'district_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:districts,id',
            ],

            'upazila_ids' => 'sometimes|array',
            'upazila_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:upazilas,id',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $memberType = (int) $data['member_type'];
        $memberId = (int) $data['member_id'];
        $countryId = (int) $data['country_id'];

        $divisionIds = array_values(array_unique(array_map(
            'intval',
            $data['division_ids'] ?? []
        )));

        $districtIds = array_values(array_unique(array_map(
            'intval',
            $data['district_ids'] ?? []
        )));

        $upazilaIds = array_values(array_unique(array_map(
            'intval',
            $data['upazila_ids'] ?? []
        )));

        /*
         * Verify that the selected member exists and is active.
         */
        $memberExists = match ($memberType) {
            USER_TYPE_EMPLOYEE => Employee::whereKey($memberId)
                ->where('status', 1)
                ->exists(),

            USER_TYPE_DEALER => Partner::whereKey($memberId)
                ->where('type', USER_TYPE_DEALER)
                ->where('status', 1)
                ->exists(),

            USER_TYPE_AGENT => Partner::whereKey($memberId)
                ->where('type', USER_TYPE_AGENT)
                ->where('status', 1)
                ->exists(),

            default => false,
        };

        if (! $memberExists) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive marketing member.',
            ], 422);
        }

        /*
         * Verify selected divisions belong to the selected country.
         */
        if (! empty($divisionIds)) {
            $validDivisionCount = Division::whereIn(
                'id',
                $divisionIds
            )
                ->where('country_id', $countryId)
                ->count();

            if ($validDivisionCount !== count($divisionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more divisions do not belong to the selected country.',
                ], 422);
            }
        }

        /*
         * Verify selected districts belong to the selected country.
         */
        if (! empty($districtIds)) {
            $validDistrictCount = District::whereIn(
                'districts.id',
                $districtIds
            )
                ->join(
                    'divisions',
                    'divisions.id',
                    '=',
                    'districts.division_id'
                )
                ->where('divisions.country_id', $countryId)
                ->count();

            if ($validDistrictCount !== count($districtIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more districts do not belong to the selected country.',
                ], 422);
            }
        }

        /*
         * Verify selected upazilas belong to the selected country.
         */
        if (! empty($upazilaIds)) {
            $validUpazilaCount = Upazila::whereIn(
                'upazilas.id',
                $upazilaIds
            )
                ->join(
                    'districts',
                    'districts.id',
                    '=',
                    'upazilas.district_id'
                )
                ->join(
                    'divisions',
                    'divisions.id',
                    '=',
                    'districts.division_id'
                )
                ->where('divisions.country_id', $countryId)
                ->count();

            if ($validUpazilaCount !== count($upazilaIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more upazilas do not belong to the selected country.',
                ], 422);
            }
        }

        /*
         * Prepare all selected scope levels.
         * Do not discard divisions or districts when upazilas
         * are also selected.
         */
        $scopeGroups = [];

        if (! empty($divisionIds)) {
            $scopeGroups['division'] = $divisionIds;
        }

        if (! empty($districtIds)) {
            $scopeGroups['district'] = $districtIds;
        }

        if (! empty($upazilaIds)) {
            $scopeGroups['upazila'] = $upazilaIds;
        }

        /*
         * No division, district or upazila means the whole country.
         */
        if (empty($scopeGroups)) {
            $scopeGroups['country'] = [$countryId];
        }

        /*
         * Save the assignment and replace its scopes atomically.
         */
        $userId = auth()->id();

        $assignment = DB::transaction(function () use (
            $memberType,
            $memberId,
            $scopeGroups,
            $userId
        ) {
            $assignment = MarketingAreaAssignment::firstOrNew([
                'member_type' => $memberType,
                'member_id' => $memberId,
            ]);

            if (! $assignment->exists) {
                $assignment->created_by = $userId;
            }

            $assignment->updated_by = $userId;
            $assignment->save();

            // Remove previous scopes before inserting the new ones.
            $assignment->scopes()->delete();

            foreach ($scopeGroups as $scopeType => $scopeIds) {
                foreach ($scopeIds as $scopeId) {
                    $assignment->scopes()->create([
                        'scope_type' => $scopeType,
                        'scope_id' => $scopeId,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }

            return $assignment;
        });

        /*
         * Keep the original response keys for compatibility,
         * and also return the complete scope groups.
         */
        $allScopeIds = [];

        foreach ($scopeGroups as $scopeIds) {
            $allScopeIds = array_merge($allScopeIds, $scopeIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Marketing area saved successfully.',
            'assignment_id' => $assignment->id,
            'scope_type' => count($scopeGroups) === 1
                ? array_key_first($scopeGroups)
                : 'multiple',
            'scope_ids' => $allScopeIds,
            'scopes' => collect($scopeGroups)->map(
                function ($ids, $type) {
                    return [
                        'scope_type' => $type,
                        'scope_ids' => $ids,
                    ];
                }
            )->values(),
        ]);
    }
}
