@extends('layouts.employee')

@section('content')

<div class="dashboard-title mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h2>Marketing Chain</h2>
        <p>
            View your marketing hierarchy and team structure
        </p>
    </div>

    <div>
        <a href="{{ route('employee.dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home mr-1"></i>
            Dashboard
        </a>
    </div>
</div>


<section class="section">

    {{-- ========================================================= --}}
    {{-- My Marketing Information --}}
    {{-- ========================================================= --}}

    <div class="row mb-4">

        {{-- My Position --}}
        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">

            <div class="card h-100">

                <div class="card-header">
                    <h4>
                        <i class="fas fa-user-tie mr-2"></i>
                        My Position
                    </h4>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div
                            class="mr-3"
                            style="
                                width:55px;
                                height:55px;
                                border-radius:50%;
                                background:#6777ef;
                                color:#fff;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:22px;
                            "
                        >
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <div>

                            <h6 class="mb-1">
                                {{ $employee->full_name ?? 'Employee' }}
                            </h6>

                            <p class="mb-1 text-muted">
                                {{ optional($employee->designation)->name ?? '-' }}
                            </p>

                            <small class="text-muted">
                                Code:
                                {{ $employee->employee_code ?? '-' }}
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- My Reporting Person --}}
        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">

            <div class="card h-100">

                <div class="card-header">
                    <h4>
                        <i class="fas fa-level-up-alt mr-2"></i>
                        Reporting To
                    </h4>
                </div>

                <div class="card-body">

                    @if($parent)

                        <div class="d-flex align-items-center">

                            <div
                                class="mr-3"
                                style="
                                    width:55px;
                                    height:55px;
                                    border-radius:50%;
                                    background:#54ca68;
                                    color:#fff;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:21px;
                                "
                            >
                                @if(($parent['type'] ?? null) == USER_TYPE_DEALER)
                                    <i class="fas fa-store"></i>
                                @elseif(($parent['type'] ?? null) == USER_TYPE_AGENT)
                                    <i class="fas fa-user-friends"></i>
                                @else
                                    <i class="fas fa-user-tie"></i>
                                @endif
                            </div>

                            <div>

                                <h6 class="mb-1">
                                    {{ $parent['name'] ?? '-' }}
                                </h6>

                                <p class="mb-1 text-muted">
                                    {{ $parent['designation'] ?? $parent['entity_type'] ?? '-' }}
                                </p>

                                <span class="badge badge-success">
                                    {{ $parent['entity_type'] ?? 'Employee' }}
                                </span>

                            </div>

                        </div>

                    @else

                        <div class="text-center py-3">

                            <i
                                class="fas fa-user-slash text-muted"
                                style="font-size:30px;"
                            ></i>

                            <p class="text-muted mb-0 mt-2">
                                No reporting person assigned
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- Direct Downline --}}
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3">

            <div class="card h-100">

                <div class="card-header">
                    <h4>
                        <i class="fas fa-users mr-2"></i>
                        My Direct Team
                    </h4>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div
                            class="mr-3"
                            style="
                                width:55px;
                                height:55px;
                                border-radius:50%;
                                background:#ffa426;
                                color:#fff;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:21px;
                            "
                        >
                            <i class="fas fa-users"></i>
                        </div>

                        <div>

                            <h3 class="mb-0">
                                {{ count($downline ?? []) }}
                            </h3>

                            <p class="mb-0 text-muted">
                                Direct members
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Marketing Hierarchy --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-header d-flex align-items-center justify-content-between">

            <div>
                <h4 class="mb-1">
                    <i class="fas fa-sitemap mr-2"></i>
                    Full Marketing Chain
                </h4>

                <small class="text-muted">
                    Your position within the marketing hierarchy
                </small>
            </div>

            <div>

                <span class="badge badge-primary">
                    <i class="fas fa-eye mr-1"></i>
                    View Only
                </span>

            </div>

        </div>


        <div class="card-body">

            @if(!empty($chain))

                <div
                    id="marketingChain"
                    class="marketing-chain-container"
                >

                    @php
                        /*
                        |--------------------------------------------------------------------------
                        | Recursive Chain Renderer
                        |--------------------------------------------------------------------------
                        */
                        $renderChainNode = function (
                            $node,
                            $level = 0,
                            $parentName = null
                        ) use (&$renderChainNode) {

                            $type = (int) ($node['type'] ?? 0);

                            $isCurrent =
                                !empty(
                                    $node['is_current_employee']
                                );

                            $entityType =
                                $node['entity_type'] ?? 'Unknown';

                            /*
                            |--------------------------------------------------------------------------
                            | Icon
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $type === USER_TYPE_DEALER
                            ) {

                                $icon = 'fa-store';

                            } elseif (
                                $type === USER_TYPE_AGENT
                            ) {

                                $icon = 'fa-user-friends';

                            } else {

                                $icon = 'fa-user-tie';

                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Badge
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $type === USER_TYPE_DEALER
                            ) {

                                $badgeClass = 'badge-warning';

                            } elseif (
                                $type === USER_TYPE_AGENT
                            ) {

                                $badgeClass = 'badge-info';

                            } else {

                                $badgeClass = 'badge-primary';

                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Current employee class
                            |--------------------------------------------------------------------------
                            */

                            $cardClass =
                                $isCurrent
                                    ? 'current-employee-node'
                                    : '';

                            /*
                            |--------------------------------------------------------------------------
                            | Node
                            |--------------------------------------------------------------------------
                            */

                            echo '<div class="marketing-chain-node ' .
                                $cardClass .
                                '" style="margin-left:' .
                                ($level * 35) .
                                'px;">';

                            /*
                            |--------------------------------------------------------------------------
                            | Connector
                            |--------------------------------------------------------------------------
                            */

                            if ($level > 0) {

                                echo '<div class="chain-connector">
                                        <i class="fas fa-level-down-alt"></i>
                                      </div>';
                            }

                            echo '<div class="chain-node-card">';

                            /*
                            |--------------------------------------------------------------------------
                            | Icon
                            |--------------------------------------------------------------------------
                            */

                            echo '<div class="chain-node-icon ' .
                                $badgeClass .
                                '">
                                    <i class="fas ' .
                                $icon .
                                '"></i>
                                  </div>';

                            /*
                            |--------------------------------------------------------------------------
                            | Information
                            |--------------------------------------------------------------------------
                            */

                            echo '<div class="chain-node-info">';

                            echo '<div class="d-flex align-items-center flex-wrap">';

                            echo '<h6 class="mb-0 mr-2">' .
                                e($node['name'] ?? '-') .
                                '</h6>';

                            if ($isCurrent) {

                                echo '<span class="badge badge-success mr-1">
                                        <i class="fas fa-user-check mr-1"></i>
                                        You
                                      </span>';
                            }

                            echo '<span class="badge ' .
                                $badgeClass .
                                '">
                                    ' .
                                e($entityType) .
                                '
                                  </span>';

                            echo '</div>';

                            /*
                            |--------------------------------------------------------------------------
                            | Designation
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !empty(
                                    $node['designation']
                                )
                            ) {

                                echo '<div class="text-muted small mt-1">
                                        <i class="fas fa-id-badge mr-1"></i>
                                        ' .
                                    e(
                                        $node['designation']
                                    ) .
                                    '
                                      </div>';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Employee Code
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !empty(
                                    $node['employee_code']
                                )
                            ) {

                                echo '<div class="text-muted small mt-1">
                                        <i class="fas fa-hashtag mr-1"></i>
                                        Employee Code:
                                        ' .
                                    e(
                                        $node['employee_code']
                                    ) .
                                    '
                                      </div>';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Under
                            |--------------------------------------------------------------------------
                            */

                            if ($parentName) {

                                echo '<div class="text-muted small mt-1">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        Under:
                                        ' .
                                    e($parentName) .
                                    '
                                      </div>';
                            }

                            echo '</div>';

                            /*
                            |--------------------------------------------------------------------------
                            | Level
                            |--------------------------------------------------------------------------
                            */

                            echo '<div class="chain-node-level text-muted small">
                                    Level ' .
                                ($level + 1) .
                                '
                                  </div>';

                            echo '</div>';

                            echo '</div>';


                            /*
                            |--------------------------------------------------------------------------
                            | Children
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !empty(
                                    $node['children']
                                )
                            ) {

                                foreach (
                                    $node['children']
                                    as $child
                                ) {

                                    $renderChainNode(
                                        $child,
                                        $level + 1,
                                        $node['name'] ?? null
                                    );
                                }
                            }
                        };
                    @endphp


                    @foreach($chain as $root)

                        @php
                            $renderChainNode(
                                $root,
                                0,
                                null
                            );
                        @endphp

                    @endforeach

                </div>

            @else

                {{-- No Chain --}}
                <div class="text-center py-5">

                    <div
                        class="mb-3"
                        style="
                            width:70px;
                            height:70px;
                            border-radius:50%;
                            background:#f0f3ff;
                            color:#6777ef;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            margin:auto;
                            font-size:30px;
                        "
                    >
                        <i class="fas fa-sitemap"></i>
                    </div>

                    <h5>
                        No Marketing Chain Found
                    </h5>

                    <p class="text-muted mb-0">
                        No marketing hierarchy has been assigned
                        to your account yet.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Direct Downline Details --}}
    {{-- ========================================================= --}}

    @if(!empty($downline))

        <div class="card mt-4">

            <div class="card-header">

                <h4>
                    <i class="fas fa-users mr-2"></i>
                    My Direct Team
                </h4>

            </div>

            <div class="card-body">

                <div class="row">

                    @foreach($downline as $member)

                        @php

                            $memberType =
                                (int) (
                                    $member['type'] ?? 0
                                );

                            if (
                                $memberType ===
                                USER_TYPE_DEALER
                            ) {

                                $memberIcon =
                                    'fa-store';

                                $memberBadge =
                                    'badge-warning';

                            } elseif (
                                $memberType ===
                                USER_TYPE_AGENT
                            ) {

                                $memberIcon =
                                    'fa-user-friends';

                                $memberBadge =
                                    'badge-info';

                            } else {

                                $memberIcon =
                                    'fa-user-tie';

                                $memberBadge =
                                    'badge-primary';
                            }

                        @endphp


                        <div
                            class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3"
                        >

                            <div class="card border h-100">

                                <div class="card-body">

                                    <div class="d-flex align-items-center">

                                        <div
                                            class="mr-3 {{ $memberBadge }}"
                                            style="
                                                width:48px;
                                                height:48px;
                                                border-radius:50%;
                                                display:flex;
                                                align-items:center;
                                                justify-content:center;
                                                font-size:18px;
                                            "
                                        >

                                            <i
                                                class="fas {{ $memberIcon }}"
                                            ></i>

                                        </div>


                                        <div>

                                            <h6 class="mb-1">
                                                {{ $member['name'] ?? '-' }}
                                            </h6>

                                            <p class="text-muted mb-1">
                                                {{ $member['designation'] ?? $member['entity_type'] ?? '-' }}
                                            </p>

                                            <span
                                                class="badge {{ $memberBadge }}"
                                            >
                                                {{ $member['entity_type'] ?? 'Member' }}
                                            </span>

                                        </div>

                                    </div>


                                    @if(!empty($member['children']))

                                        <div class="mt-3 pt-3 border-top">

                                            <small class="text-muted">
                                                <i class="fas fa-users mr-1"></i>

                                                Downline:

                                                <strong>
                                                    {{ count($member['children']) }}
                                                </strong>
                                            </small>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    @endif

</section>


{{-- ============================================================= --}}
{{-- Custom Styles --}}
{{-- ============================================================= --}}

<style>

    .marketing-chain-container {
        width: 100%;
        overflow-x: auto;
        padding: 10px 5px 20px;
    }

    .marketing-chain-node {
        position: relative;
        margin-bottom: 12px;
    }

    .chain-node-card {
        min-height: 75px;
        border: 1px solid #e4e6ef;
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        padding: 12px 15px;
        transition: all 0.2s ease;
    }

    .chain-node-card:hover {
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    }

    .chain-node-icon {
        width: 45px;
        height: 45px;
        min-width: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        margin-right: 12px;
    }

    .chain-node-info {
        flex: 1;
        min-width: 0;
    }

    .chain-node-info h6 {
        font-size: 15px;
        font-weight: 600;
    }

    .chain-node-level {
        white-space: nowrap;
        margin-left: 10px;
    }

    .chain-connector {
        position: absolute;
        left: -25px;
        top: -12px;
        width: 20px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6777ef;
        font-size: 12px;
    }

    .current-employee-node .chain-node-card {
        border: 2px solid #6777ef;
        box-shadow: 0 4px 15px rgba(103, 119, 239, 0.15);
    }

    .current-employee-node .chain-node-icon {
        width: 50px;
        height: 50px;
        min-width: 50px;
    }

    @media (max-width: 768px) {

        .chain-node-card {
            padding: 10px;
        }

        .chain-node-level {
            display: none;
        }

        .marketing-chain-node {
            margin-left: 0 !important;
        }

        .chain-connector {
            display: none;
        }

    }

</style>

@endsection