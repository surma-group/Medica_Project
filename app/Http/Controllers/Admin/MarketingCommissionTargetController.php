<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Partner;
use App\Models\MarketingMemberTarget;
use App\Models\MarketingMemberCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MarketingCommissionTargetController extends Controller
{
    /**
     * Display Commission & Target page.
     */
    public function index()
    {
        // Active Employees
        $employees = Employee::with('designation')
            ->where('status', 1)
            ->orderBy('full_name')
            ->get();

        // Active Dealers & Agents
        $partners = Partner::whereIn('type', [
                USER_TYPE_DEALER,
                USER_TYPE_AGENT,
            ])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // Dealers
        $dealers = $partners
            ->where('type', USER_TYPE_DEALER)
            ->values();

        // Agents
        $agents = $partners
            ->where('type', USER_TYPE_AGENT)
            ->values();

        return view(
            'admin.marketing.commission-target',
            compact(
                'employees',
                'dealers',
                'agents'
            )
        );
    }

    /**
     * Save or update a member's sales target.
     */
    public function saveTarget(Request $request)
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

            'member_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'period_type' => [
                'required',
                'integer',
                Rule::in([
                    PERIOD_TYPE_DAILY,
                    PERIOD_TYPE_WEEKLY,
                    PERIOD_TYPE_MONTHLY,
                    PERIOD_TYPE_YEARLY,
                ]),
            ],

            'target_type' => [
                'required',
                'integer',
                Rule::in([
                    TYPE_FLAT,
                    TYPE_PERCENTAGE,
                ]),
            ],

            'target_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
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

        // Verify that the selected member exists and is active.
        $exists = match ((int) $data['member_type']) {
            USER_TYPE_EMPLOYEE => Employee::whereKey($data['member_id'])
                ->where('status', 1)
                ->exists(),

            USER_TYPE_DEALER => Partner::whereKey($data['member_id'])
                ->where('type', USER_TYPE_DEALER)
                ->where('status', 1)
                ->exists(),

            USER_TYPE_AGENT => Partner::whereKey($data['member_id'])
                ->where('type', USER_TYPE_AGENT)
                ->where('status', 1)
                ->exists(),

            default => false,
        };

        if (! $exists) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive marketing member.',
            ], 422);
        }

        // Update the existing target or create a new one.
        MarketingMemberTarget::updateOrCreate(
            [
                'member_type' => $data['member_type'],
                'member_id' => $data['member_id'],
                'period_type' => $data['period_type'],
            ],
            [
                'target_type' => $data['target_type'],
                'target_amount' => $data['target_amount'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Sales target saved successfully.',
        ]);
    }

    /**
     * Save or update a member's commission.
     */
    public function saveCommission(Request $request)
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

            'member_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'commission_type' => [
                'required',
                'integer',
                Rule::in([
                    COMMISSION_TYPE_FLAT,
                    COMMISSION_TYPE_PERCENTAGE,
                ]),
            ],

            'commission_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
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

        // Verify that the selected member exists and is active.
        $exists = match ((int) $data['member_type']) {
            USER_TYPE_EMPLOYEE => Employee::whereKey($data['member_id'])
                ->where('status', 1)
                ->exists(),

            USER_TYPE_DEALER => Partner::whereKey($data['member_id'])
                ->where('type', USER_TYPE_DEALER)
                ->where('status', 1)
                ->exists(),

            USER_TYPE_AGENT => Partner::whereKey($data['member_id'])
                ->where('type', USER_TYPE_AGENT)
                ->where('status', 1)
                ->exists(),

            default => false,
        };

        if (! $exists) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive marketing member.',
            ], 422);
        }

        // Update the existing commission or create a new one.
        MarketingMemberCommission::updateOrCreate(
            [
                'member_type' => $data['member_type'],
                'member_id' => $data['member_id'],
            ],
            [
                'commission_type' => $data['commission_type'],
                'commission_amount' => $data['commission_amount'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Commission saved successfully.',
        ]);
    }
}
