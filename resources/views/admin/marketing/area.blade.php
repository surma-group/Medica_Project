@extends('layouts.admin')

@section('title', 'Marketing Area')

@push('styles')
<style>
    .marketing-page .card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .04);
    }

    .marketing-page .card-header {
        padding: 20px;
        background: #fff;
        border-bottom: 1px solid #eee;
    }

    .marketing-page .card-body {
        padding: 20px;
    }

    .marketing-page .page-subtitle {
        color: #6c757d;
        font-size: 13px;
        margin-top: 4px;
    }

    .marketing-page .member-row:hover {
        background: #f8faff;
    }

    .marketing-page .btn {
        border-radius: 6px;
    }

    .marketing-page .filter-panel {
        padding: 18px;
        border: 1px solid #e6eaf0;
        background: #f8f9fc;
        border-radius: 10px;
        margin-bottom: 22px;
    }

    .marketing-page .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 7px;
        color: #343a40;
    }

    .marketing-page .form-control {
        min-height: 40px;
        border-radius: 7px;
    }

    .marketing-page .member-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    #marketingAreaModal,
    #viewMarketingAreaModal {
        z-index: 1055;
        overflow: hidden;
    }

    #marketingAreaModal .modal-dialog,
    #viewMarketingAreaModal .modal-dialog {
        width: calc(100% - 24px);
        max-width: 1050px;
        margin: 12px auto;
    }

    #marketingAreaModal .modal-content,
    #viewMarketingAreaModal .modal-content {
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 24px);
        max-height: calc(100dvh - 24px);
        overflow: hidden;
        border: 0;
        border-radius: 12px;
        background: #fff;
    }

    #marketingAreaModal .modal-header,
    #viewMarketingAreaModal .modal-header {
        flex: 0 0 auto;
        padding: 18px 22px;
        background: #fff;
        border-bottom: 1px solid #eee;
    }

    #marketingAreaModal .modal-body,
    #viewMarketingAreaModal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        max-height: calc(100vh - 175px);
        max-height: calc(100dvh - 175px);
        overflow-y: auto !important;
        overflow-x: hidden;
        padding: 22px;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }

    #marketingAreaModal .modal-footer,
    #viewMarketingAreaModal .modal-footer {
        flex: 0 0 auto;
        padding: 14px 22px;
        background: #fff;
        border-top: 1px solid #eee;
    }

    #marketingAreaModal .form-label {
        display: block;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 8px;
    }

    #marketingAreaModal .form-control {
        width: 100%;
        min-height: 42px;
        border-radius: 7px;
    }

    #marketingAreaModal select[multiple].form-control {
        height: 145px;
        min-height: 145px;
        padding: 6px;
        background: #fff;
    }

    #marketingAreaModal select[multiple].form-control option {
        padding: 7px 8px;
        white-space: normal;
    }

    #marketingAreaModal .area-panel {
        height: 100%;
        padding: 18px;
        border: 1px solid #e4e8ef;
        border-radius: 10px;
        background: #fff;
    }

    #marketingAreaModal .area-panel-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    #marketingAreaModal .area-help {
        display: block;
        color: #6c757d;
        font-size: 12px;
        line-height: 1.6;
        margin-top: 7px;
    }

    #marketingAreaModal .add-area-btn {
        margin-top: 8px;
    }

    #marketingAreaModal .selected-area-panel {
        background: #f8f9fc;
    }

    #marketingAreaModal .selected-area-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 11px;
        margin-bottom: 9px;
        background: #fff;
        border: 1px solid #e3e7ed;
        border-radius: 8px;
    }

    #marketingAreaModal .selected-area-name {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    #marketingAreaModal .selected-area-type {
        color: #6c757d;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    #marketingAreaModal .remove-area {
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        border: 0;
        border-radius: 6px;
        background: #fee2e2;
        color: #dc2626;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
    }

    #marketingAreaModal .area-empty {
        padding: 28px 12px;
        color: #7b8490;
        text-align: center;
        font-size: 13px;
    }

    #marketingAreaModal .area-panel .form-group {
        margin-bottom: 22px;
    }

    #marketingAreaModal .area-panel .form-group:last-child {
        margin-bottom: 0;
    }

    #viewMarketingAreaModal .view-scope-item {
        padding: 13px 15px;
        border: 1px solid #e5e9f0;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #fff;
    }

    #viewMarketingAreaModal .view-scope-type {
        color: #6c757d;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    #viewMarketingAreaModal .view-empty {
        padding: 28px 12px;
        color: #7b8490;
        text-align: center;
        background: #f8f9fc;
        border-radius: 8px;
    }

    @media (max-width: 767px) {

        .marketing-page .card-header,
        .marketing-page .card-body {
            padding: 15px;
        }

        #marketingAreaModal .modal-dialog,
        #viewMarketingAreaModal .modal-dialog {
            width: calc(100% - 16px);
            margin: 8px auto;
        }

        #marketingAreaModal .modal-content,
        #viewMarketingAreaModal .modal-content {
            max-height: calc(100vh - 16px);
            max-height: calc(100dvh - 16px);
        }

        #marketingAreaModal .modal-body,
        #viewMarketingAreaModal .modal-body {
            max-height: calc(100vh - 145px);
            max-height: calc(100dvh - 145px);
            padding: 15px;
        }

        #marketingAreaModal .modal-header,
        #marketingAreaModal .modal-footer,
        #viewMarketingAreaModal .modal-header,
        #viewMarketingAreaModal .modal-footer {
            padding: 13px 15px;
        }

        #marketingAreaModal .area-panel {
            padding: 13px;
        }

        #marketingAreaModal select[multiple].form-control {
            height: 125px;
            min-height: 125px;
        }
    }


    /* Tree view for Selected Areas */
    #marketingAreaModal .area-tree,
    #viewMarketingAreaModal .area-tree {
        padding: 4px 0;
    }

    .area-tree-node {
        position: relative;
        margin-left: 13px;
        padding-left: 18px;
        border-left: 1px solid #d8dee8;
    }

    .area-tree-node:last-child {
        border-left-color: transparent;
    }

    .area-tree-row {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        padding: 9px 8px;
        margin: 3px 0;
        border: 1px solid #e5e9f0;
        border-radius: 7px;
        background: #fff;
    }

    .area-tree-icon {
        flex-shrink: 0;
        width: 18px;
        text-align: center;
        color: #64748b;
    }

    .area-tree-label {
        flex: 1;
        min-width: 0;
        overflow-wrap: anywhere;
        font-size: 13px;
        font-weight: 600;
    }

    .area-tree-kind {
        flex-shrink: 0;
        font-size: 10px;
        color: #64748b;
        background: #f1f5f9;
        border-radius: 4px;
        padding: 3px 6px;
    }

    .area-tree-context .area-tree-row {
        background: #f8fafc;
        border-style: dashed;
    }

    .area-tree-context .area-tree-label {
        font-weight: 400;
        color: #64748b;
    }

    .area-tree-children {
        padding-left: 5px;
    }

    #marketingAreaModal .area-tree-remove {
        flex-shrink: 0;
        width: 27px;
        height: 27px;
        border: 0;
        border-radius: 5px;
        color: #dc2626;
        background: #fee2e2;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
    }

    #marketingAreaModal .area-tree-remove:hover {
        background: #fecaca;
    }

    #viewMarketingAreaModal .area-tree-row {
        padding: 12px 10px;
    }
</style>
@endpush

@section('content')
<div class="section marketing-page">
    <div class="section-header">
        <h1>Marketing Area</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Marketing</div>
            <div class="breadcrumb-item">Marketing Area</div>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Marketing Members</h4>
                    <div class="page-subtitle">
                        Filter members by working area and manage their assignments.
                    </div>
                </div>

                <span class="badge badge-primary">
                    {{ $employees->count() + $dealers->count() + $agents->count() }}
                    Members
                </span>
            </div>

            <div class="card-body">
                {{-- Area filter --}}
                <div class="filter-panel">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="filterMemberType" class="form-label">
                                Member Type
                            </label>
                            <select id="filterMemberType" class="form-control">
                                <option value="all">All Members</option>
                                <option value="employee">Employee</option>
                                <option value="dealer">Dealer</option>
                                <option value="agent">Agent</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="filterDivision" class="form-label">
                                Division
                            </label>
                            <select id="filterDivision" class="form-control">
                                <option value="">All Divisions</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="filterDistrict" class="form-label">
                                District
                            </label>
                            <select id="filterDistrict" class="form-control" disabled>
                                <option value="">All Districts</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="filterUpazila" class="form-label">
                                Upazila / Thana
                            </label>
                            <select id="filterUpazila" class="form-control" disabled>
                                <option value="">All Upazilas</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center">
                        <button type="button" id="applyAreaFilter" class="btn btn-primary mr-2 mb-2">
                            <i class="fas fa-filter"></i> Filter Members
                        </button>

                        <button type="button" id="resetAreaFilter" class="btn btn-light border mr-3 mb-2">
                            <i class="fas fa-redo"></i> Reset
                        </button>

                        <span id="filterStatus" class="small text-muted mb-2">
                            Showing all members.
                        </span>
                    </div>
                </div>

                <div id="memberListMessage" class="alert d-none" role="alert"></div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width:60px">SL</th>
                                <th>Member Name</th>
                                <th>Code / Shop</th>
                                <th>Type</th>
                                <th>Working Area</th>
                                <th style="min-width:190px">Action</th>
                            </tr>
                        </thead>

                        <tbody id="marketingMembersBody">
                            @php $sl = 0; @endphp

                            @foreach($employees as $employee)
                            @php
                            $sl++;
                            $employeeName = $employee->full_name ?? 'Unnamed Employee';
                            $employeeCode = $employee->employee_code ?? '';
                            @endphp

                            <tr class="member-row"
                                data-member-type="{{ USER_TYPE_EMPLOYEE }}"
                                data-member-id="{{ $employee->id }}"
                                data-type-label="employee">
                                <td>{{ $sl }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $employeeName }}</div>
                                    @if($employee->designation)
                                    <small class="text-muted">
                                        {{ $employee->designation->name ?? '' }}
                                    </small>
                                    @endif
                                </td>
                                <td>{{ $employeeCode ?: '—' }}</td>
                                <td><span class="badge badge-primary">Employee</span></td>
                                <td class="member-area-cell">
                                    <span class="text-muted">Loading assignment...</span>
                                </td>
                                <td>
                                    <div class="member-actions">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-info view-area-btn"
                                            data-member-type="{{ USER_TYPE_EMPLOYEE }}"
                                            data-member-id="{{ $employee->id }}"
                                            data-type-label="Employee"
                                            data-member-name="{{ $employeeName }}">
                                            <i class="fas fa-eye"></i> View Area
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-primary assign-area-btn"
                                            data-member-type="{{ USER_TYPE_EMPLOYEE }}"
                                            data-member-id="{{ $employee->id }}"
                                            data-type-label="Employee"
                                            data-member-name="{{ $employeeName }}">
                                            <i class="fas fa-map-marker-alt"></i> Assign/Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                            @foreach($dealers as $dealer)
                            @php
                            $sl++;
                            $dealerName = $dealer->name ?? 'Unnamed Dealer';
                            $dealerShop = $dealer->shop_name ?? '';
                            @endphp

                            <tr class="member-row"
                                data-member-type="{{ USER_TYPE_DEALER }}"
                                data-member-id="{{ $dealer->id }}"
                                data-type-label="dealer">
                                <td>{{ $sl }}</td>
                                <td class="font-weight-bold">{{ $dealerName }}</td>
                                <td>{{ $dealerShop ?: '—' }}</td>
                                <td><span class="badge badge-success">Dealer</span></td>
                                <td class="member-area-cell">
                                    <span class="text-muted">Loading assignment...</span>
                                </td>
                                <td>
                                    <div class="member-actions">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-info view-area-btn"
                                            data-member-type="{{ USER_TYPE_DEALER }}"
                                            data-member-id="{{ $dealer->id }}"
                                            data-type-label="Dealer"
                                            data-member-name="{{ $dealerName }}">
                                            <i class="fas fa-eye"></i> View Area
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-primary assign-area-btn"
                                            data-member-type="{{ USER_TYPE_DEALER }}"
                                            data-member-id="{{ $dealer->id }}"
                                            data-type-label="Dealer"
                                            data-member-name="{{ $dealerName }}">
                                            <i class="fas fa-map-marker-alt"></i> Assign/Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                            @foreach($agents as $agent)
                            @php
                            $sl++;
                            $agentName = $agent->name ?? 'Unnamed Agent';
                            $agentShop = $agent->shop_name ?? '';
                            @endphp

                            <tr class="member-row"
                                data-member-type="{{ USER_TYPE_AGENT }}"
                                data-member-id="{{ $agent->id }}"
                                data-type-label="agent">
                                <td>{{ $sl }}</td>
                                <td class="font-weight-bold">{{ $agentName }}</td>
                                <td>{{ $agentShop ?: '—' }}</td>
                                <td><span class="badge badge-info">Agent</span></td>
                                <td class="member-area-cell">
                                    <span class="text-muted">Loading assignment...</span>
                                </td>
                                <td>
                                    <div class="member-actions">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-info view-area-btn"
                                            data-member-type="{{ USER_TYPE_AGENT }}"
                                            data-member-id="{{ $agent->id }}"
                                            data-type-label="Agent"
                                            data-member-name="{{ $agentName }}">
                                            <i class="fas fa-eye"></i> View Area
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-primary assign-area-btn"
                                            data-member-type="{{ USER_TYPE_AGENT }}"
                                            data-member-id="{{ $agent->id }}"
                                            data-type-label="Agent"
                                            data-member-name="{{ $agentName }}">
                                            <i class="fas fa-map-marker-alt"></i> Assign/Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                            <tr id="noMembersFound" style="display:none">
                                <td colspan="6" class="text-center text-muted py-4">
                                    No members found for this area.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Assign / Edit Area Modal --}}
<div class="modal fade" id="marketingAreaModal" tabindex="-1"
    role="dialog" aria-labelledby="marketingAreaModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="marketingAreaForm">
                @csrf

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title font-weight-bold" id="marketingAreaModalLabel">
                            Assign Marketing Area
                        </h5>
                        <div id="assignmentMemberInfo" class="small text-muted mt-1"></div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="assignmentMemberType" name="member_type">
                    <input type="hidden" id="assignmentMemberId" name="member_id">

                    <div id="marketingAreaMessage" class="alert d-none" role="alert"></div>

                    <div class="mb-4">
                        <label for="areaCountry" class="form-label">
                            Country <span class="text-danger">*</span>
                        </label>

                        <select id="areaCountry" name="country_id" class="form-control" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->name_en }}
                                @if(!empty($country->name_bn))
                                / {{ $country->name_bn }}
                                @endif
                            </option>
                            @endforeach
                        </select>

                        <small class="text-muted d-block mt-2">
                            শুধু Country নির্বাচন করলে পুরো Country বরাদ্দ হবে।
                            নির্দিষ্ট এলাকা বরাদ্দ করতে নিচের তালিকা থেকে নির্বাচন করে Add করো।
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="area-panel">
                                <div class="area-panel-title">
                                    <i class="fas fa-map-marked-alt text-primary"></i>
                                    Choose Areas
                                </div>

                                <div class="form-group">
                                    <label for="areaDivisions" class="form-label">Division</label>
                                    <select id="areaDivisions" class="form-control" multiple disabled></select>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary add-area-btn"
                                        data-level="division">
                                        + Add Divisions
                                    </button>

                                    <small class="area-help">
                                        একাধিক নির্বাচন করতে Ctrl চেপে রাখো।
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="areaDistricts" class="form-label">District</label>
                                    <select id="areaDistricts" class="form-control" multiple disabled></select>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary add-area-btn"
                                        data-level="district">
                                        + Add Districts
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label for="areaUpazilas" class="form-label">Upazila / Thana</label>
                                    <select id="areaUpazilas" class="form-control" multiple disabled></select>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary add-area-btn"
                                        data-level="upazila">
                                        + Add Upazilas
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="area-panel selected-area-panel">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="area-panel-title mb-0">
                                        <i class="fas fa-list text-success"></i>
                                        Selected Areas
                                    </div>
                                    <span class="badge badge-primary" id="selectedAreaCount">0</span>
                                </div>

                                <div id="selectedAreaList">
                                    <div class="area-empty">
                                        <i class="fas fa-map fa-2x mb-2"></i>
                                        <div>No specific areas added.</div>
                                        <small>Country-only assignment needs no additional area.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary" id="saveMarketingAreaBtn">
                        <span class="save-btn-text">
                            <i class="fas fa-save"></i> Save Area
                        </span>
                        <span class="save-btn-loading d-none">
                            <span class="spinner-border spinner-border-sm"></span>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Existing Area Modal --}}
<div class="modal fade" id="viewMarketingAreaModal" tabindex="-1"
    role="dialog" aria-labelledby="viewMarketingAreaModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title font-weight-bold" id="viewMarketingAreaModalLabel">
                        Assigned Working Area
                    </h5>
                    <div id="viewAreaMemberInfo" class="small text-muted mt-1"></div>
                </div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="viewAreaMessage" class="alert d-none" role="alert"></div>
                <div id="viewAreaContent"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal">
                    Close
                </button>

                <button type="button" class="btn btn-primary" id="editFromViewBtn">
                    <i class="fas fa-edit"></i> Edit Area
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function($) {
        'use strict';

        $(function() {
            const urls = {
                divisions: @json(route('marketing.area.divisions')),
                districts: @json(route('marketing.area.districts')),
                upazilas: @json(route('marketing.area.upazilas')),
                assignment: @json(route('marketing.area.assignment')),
                save: @json(route('marketing.area.save')),
                filterMembers: @json(route('marketing.area.filter-members'))
            };

            const csrfToken = @json(csrf_token());

            let selectedAreas = [];
            const labels = {
                country: 'Country',
                division: 'Division',
                district: 'District',
                upazila: 'Upazila'
            };
            let memberCache = {};
            let filterVersions = {
                divisions: 0,
                districts: 0,
                upazilas: 0
            };


            const areaNodes = {
                division: {},
                district: {},
                upazila: {}
            };

            function areaDisplayName(item) {
                if (item.name_en && item.name_bn) {
                    return item.name_en + ' / ' + item.name_bn;
                }

                return item.name_en || item.name_bn || item.name || 'Unknown area';
            }

            function cacheAreaItems(type, items) {
                (items || []).forEach(function(item) {
                    const parentId = type === 'district' ?
                        item.division_id :
                        (type === 'upazila' ? item.district_id : null);

                    areaNodes[type][String(item.id)] = {
                        id: String(item.id),
                        type: type,
                        name: areaDisplayName(item),
                        parentId: parentId == null ? null : String(parentId)
                    };
                });
            }

            function showAlert(selector, message, type) {
                $(selector)
                    .removeClass('d-none alert-danger alert-success alert-warning alert-info')
                    .addClass('alert-' + (type || 'danger'))
                    .text(message);
            }

            function hideAlert(selector) {
                $(selector)
                    .addClass('d-none')
                    .removeClass('alert-danger alert-success alert-warning alert-info')
                    .text('');
            }

            function getJson(url, data) {
                return $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
            }

            function postJson(url, data) {
                return $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
            }

            function selectedValues(selector) {
                return ($(selector).val() || []).map(String);
            }

            function setSelectOptions(selector, items, placeholder) {
                const $select = $(selector);
                $select.empty();

                if (placeholder) {
                    $select.append(new Option(placeholder, ''));
                }

                (items || []).forEach(function(item) {
                    let name = item.name_en || item.name_bn || '';

                    if (item.name_en && item.name_bn) {
                        name = item.name_en + ' / ' + item.name_bn;
                    }

                    $select.append(new Option(name, String(item.id)));
                });

                $select.prop('disabled', !items || items.length === 0);
                $select.val('');
            }


            function setMultipleOptions(selector, items) {
                const $select = $(selector);
                $select.empty();

                const typeMap = {
                    '#areaDivisions': 'division',
                    '#areaDistricts': 'district',
                    '#areaUpazilas': 'upazila'
                };

                const type = typeMap[selector];

                if (type) {
                    cacheAreaItems(type, items);
                }

                (items || []).forEach(function(item) {
                    const name = areaDisplayName(item);
                    const option = new Option(name, String(item.id));

                    if (type === 'district' && item.division_id != null) {
                        option.dataset.parentId = String(item.division_id);
                    }

                    if (type === 'upazila' && item.district_id != null) {
                        option.dataset.parentId = String(item.district_id);
                    }

                    $select.append(option);
                });

                $select.prop('disabled', !items || items.length === 0);
                $select.val([]);
            }


            function resetSelect(selector, placeholder) {
                const $select = $(selector);
                $select.empty();

                if (placeholder) {
                    $select.append(new Option(placeholder, ''));
                }

                $select.val('').prop('disabled', true);
            }

            function resetMultipleSelect(selector) {
                $(selector).empty().val([]).prop('disabled', true);
            }

            function fetchDivisions(countryId) {
                if (!countryId) {
                    return $.Deferred().resolve([]).promise();
                }

                return getJson(urls.divisions, {
                        country_id: countryId
                    })
                    .then(function(response) {
                        return response.success ? response.data : [];
                    });
            }

            function fetchDistricts(divisionIds) {
                if (!divisionIds || !divisionIds.length) {
                    return $.Deferred().resolve([]).promise();
                }

                return getJson(urls.districts, {
                        division_ids: divisionIds
                    })
                    .then(function(response) {
                        return response.success ? response.data : [];
                    });
            }

            function fetchUpazilas(districtIds) {
                if (!districtIds || !districtIds.length) {
                    return $.Deferred().resolve([]).promise();
                }

                return getJson(urls.upazilas, {
                        district_ids: districtIds
                    })
                    .then(function(response) {
                        return response.success ? response.data : [];
                    });
            }

            /*
             * Area filter dropdowns
             */
            function loadFilterDivisions() {
                const version = ++filterVersions.divisions;

                resetSelect('#filterDivision', 'All Divisions');
                resetSelect('#filterDistrict', 'All Districts');
                resetSelect('#filterUpazila', 'All Upazilas');

                fetchDivisionsForFilter(version);
            }

            function fetchDivisionsForFilter(version) {
                /*
                 * The application currently has a Country selector only in the
                 * assignment modal. For the area filter, use the selected Country
                 * when available; otherwise use the first configured Country.
                 */
                const firstCountryId = $('#areaCountry option').filter(function() {
                    return this.value !== '';
                }).first().val();

                if (!firstCountryId) return;

                getJson(urls.divisions, {
                        country_id: firstCountryId
                    })
                    .done(function(response) {
                        if (version !== filterVersions.divisions) return;

                        if (response.success) {
                            setSelectOptions(
                                '#filterDivision',
                                response.data || [],
                                'All Divisions'
                            );
                        }
                    })
                    .fail(function() {
                        showAlert('#memberListMessage', 'Filter Division load করা যায়নি।');
                    });
            }

            $('#filterDivision').on('change', function() {
                const divisionId = $(this).val();

                resetSelect('#filterDistrict', 'All Districts');
                resetSelect('#filterUpazila', 'All Upazilas');

                if (!divisionId) return;

                const version = ++filterVersions.districts;

                fetchDistricts([divisionId])
                    .done(function(districts) {
                        if (version !== filterVersions.districts) return;

                        setSelectOptions('#filterDistrict', districts, 'All Districts');
                    })
                    .fail(function() {
                        showAlert('#memberListMessage', 'Filter District load করা যায়নি।');
                    });
            });

            $('#filterDistrict').on('change', function() {
                const districtId = $(this).val();

                resetSelect('#filterUpazila', 'All Upazilas');

                if (!districtId) return;

                const version = ++filterVersions.upazilas;

                fetchUpazilas([districtId])
                    .done(function(upazilas) {
                        if (version !== filterVersions.upazilas) return;

                        setSelectOptions('#filterUpazila', upazilas, 'All Upazilas');
                    })
                    .fail(function() {
                        showAlert('#memberListMessage', 'Filter Upazila load করা যায়নি।');
                    });
            });

            /*
             * Show saved area labels in the table.
             */
            function scopeDisplay(scopes) {
                if (!scopes || !scopes.length) {
                    return '<span class="badge badge-secondary">Not assigned</span>';
                }

                const names = scopes.map(function(scope) {
                    const name = scope.name_bn || scope.name_en || 'Unknown area';
                    const type = labels[scope.scope_type] || scope.scope_type;

                    return $('<div>').text(type + ': ' + name).html();
                });

                return names.join('<br>');
            }

            function loadMemberAssignments() {
                $('.member-row').each(function() {
                    const $row = $(this);
                    const memberType = $row.data('member-type');
                    const memberId = $row.data('member-id');
                    const key = String(memberType) + ':' + String(memberId);

                    getJson(urls.assignment, {
                        member_type: memberType,
                        member_id: memberId
                    }).done(function(response) {
                        let scopes = [];

                        if (response && response.success && response.exists && response.data) {
                            scopes = response.data.scopes || [];
                        }

                        memberCache[key] = {
                            member_type: memberType,
                            member_id: memberId,
                            exists: !!(response && response.exists),
                            data: response ? response.data : null,
                            scopes: scopes
                        };

                        $row.find('.member-area-cell').html(scopeDisplay(scopes));
                    }).fail(function() {
                        $row.find('.member-area-cell').html(
                            '<span class="text-danger">Could not load</span>'
                        );
                    });
                });
            }

            /*
             * Apply server-side area filter.
             */
            function applyAreaFilter() {
                hideAlert('#memberListMessage');

                const data = {
                    member_type: $('#filterMemberType').val() || 'all',
                    division_id: $('#filterDivision').val() || '',
                    district_id: $('#filterDistrict').val() || '',
                    upazila_id: $('#filterUpazila').val() || ''
                };

                $('#applyAreaFilter').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span> Filtering...');

                getJson(urls.filterMembers, data)
                    .done(function(response) {
                        if (!response || !response.success) {
                            showAlert(
                                '#memberListMessage',
                                response && response.message ? response.message : 'Filter করা যায়নি।'
                            );
                            return;
                        }

                        const allowed = {};

                        (response.data || []).forEach(function(member) {
                            allowed[String(member.member_type) + ':' + String(member.member_id)] = true;
                        });

                        let count = 0;

                        $('.member-row').each(function() {
                            const $row = $(this);
                            const key = String($row.data('member-type')) + ':' +
                                String($row.data('member-id'));

                            const visible = !!allowed[key];
                            $row.toggle(visible);

                            if (visible) count++;
                        });

                        $('#noMembersFound').toggle(count === 0);

                        $('#filterStatus').text(
                            count + ' member(s) found for the selected filter.'
                        );
                    })
                    .fail(function(xhr) {
                        const message = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Area filter করার সময় সমস্যা হয়েছে।';

                        showAlert('#memberListMessage', message);
                    })
                    .always(function() {
                        $('#applyAreaFilter').prop('disabled', false)
                            .html('<i class="fas fa-filter"></i> Filter Members');
                    });
            }

            $('#applyAreaFilter').on('click', applyAreaFilter);

            $('#resetAreaFilter').on('click', function() {
                $('#filterMemberType').val('all');
                $('#filterDivision').val('');
                resetSelect('#filterDistrict', 'All Districts');
                resetSelect('#filterUpazila', 'All Upazilas');

                $('.member-row').show();
                $('#noMembersFound').hide();
                $('#filterStatus').text('Showing all members.');
                hideAlert('#memberListMessage');
            });

            /*
             * Assignment modal helpers
             */

            function clearAreaSelects() {
                resetMultipleSelect('#areaDivisions');
                resetMultipleSelect('#areaDistricts');
                resetMultipleSelect('#areaUpazilas');
            }

            function resetAssignmentModal() {
                $('#marketingAreaForm')[0].reset();

                selectedAreas = [];

                clearAreaSelects();

                hideAlert('#marketingAreaMessage');

                $('#assignmentMemberType').val('');
                $('#assignmentMemberId').val('');
                $('#assignmentMemberInfo').text('');

                renderSelectedAreas();
            }

            function renderAreaTree($container, areas, mode) {
                $container.empty();

                if (!areas.length) {
                    $container.html(
                        '<div class="' +
                        (mode === 'view' ? 'view-empty' : 'area-empty') +
                        '">' +
                        '<i class="fas fa-map-marked-alt fa-2x mb-2"></i>' +
                        '<div>' +
                        (mode === 'view' ?
                            'No area assigned yet' :
                            'No specific areas added.') +
                        '</div>' +
                        '</div>'
                    );
                    return;
                }

                const nodes = {};

                function key(type, id) {
                    return type + ':' + String(id);
                }

                function ensureNode(type, id, name, parentId, selected) {
                    if (id == null || id === '') return null;

                    const nodeKey = key(type, id);

                    if (!nodes[nodeKey]) {
                        const cached = areaNodes[type] &&
                            areaNodes[type][String(id)];

                        nodes[nodeKey] = {
                            id: String(id),
                            type: type,
                            name: name || (cached && cached.name) || 'Unknown area',
                            parentId: parentId != null ?
                                String(parentId) : (cached && cached.parentId != null ?
                                    String(cached.parentId) :
                                    null),
                            selected: !!selected,
                            children: []
                        };
                    } else if (selected) {
                        nodes[nodeKey].selected = true;
                        nodes[nodeKey].name = name || nodes[nodeKey].name;

                        if (parentId != null) {
                            nodes[nodeKey].parentId = String(parentId);
                        }
                    }

                    return nodes[nodeKey];
                }

                function ensureParents(node) {
                    if (node.type === 'district' && node.parentId) {
                        const parent = ensureNode(
                            'division',
                            node.parentId,
                            null,
                            null,
                            false
                        );

                        if (parent) ensureParents(parent);
                    }

                    if (node.type === 'upazila' && node.parentId) {
                        const parent = ensureNode(
                            'district',
                            node.parentId,
                            null,
                            null,
                            false
                        );

                        if (parent) ensureParents(parent);
                    }
                }

                areas.forEach(function(area) {
                    const node = ensureNode(
                        area.type,
                        area.id,
                        area.name,
                        area.parentId,
                        true
                    );

                    if (node) ensureParents(node);
                });

                Object.keys(nodes).forEach(function(nodeKey) {
                    const node = nodes[nodeKey];

                    let parentType = null;

                    if (node.type === 'district') parentType = 'division';
                    if (node.type === 'upazila') parentType = 'district';

                    if (parentType && node.parentId) {
                        const parent = nodes[key(parentType, node.parentId)];

                        if (parent) {
                            parent.children.push(node);
                            return;
                        }
                    }
                });

                const roots = Object.values(nodes).filter(function(node) {
                    if (node.type === 'division') return true;

                    const parentType = node.type === 'district' ?
                        'division' :
                        'district';

                    return !node.parentId ||
                        !nodes[key(parentType, node.parentId)];
                });

                function sortNodes(list) {
                    list.sort(function(a, b) {
                        return a.name.localeCompare(b.name);
                    });

                    list.forEach(function(node) {
                        sortNodes(node.children);
                    });
                }

                sortNodes(roots);

                const icons = {
                    division: 'fa-map',
                    district: 'fa-map-marked-alt',
                    upazila: 'fa-map-pin'
                };

                function drawNode(node, $parent) {
                    const $node = $('<div class="area-tree-node"></div>');

                    if (!node.selected) {
                        $node.addClass('area-tree-context');
                    }

                    const $row = $('<div class="area-tree-row"></div>');

                    $('<i class="fas area-tree-icon"></i>')
                        .addClass(icons[node.type] || 'fa-map')
                        .appendTo($row);

                    $('<span class="area-tree-label"></span>')
                        .text(node.name)
                        .appendTo($row);

                    if (!node.selected) {
                        $('<span class="area-tree-kind">Parent</span>')
                            .appendTo($row);
                    } else {
                        $('<span class="area-tree-kind"></span>')
                            .text(labels[node.type] || node.type)
                            .appendTo($row);
                    }

                    if (mode === 'selected' && node.selected) {
                        const $remove = $(
                            '<button type="button" class="area-tree-remove" aria-label="Remove area">×</button>'
                        );

                        $remove.on('click', function() {
                            selectedAreas = selectedAreas.filter(function(area) {
                                return !(
                                    area.type === node.type &&
                                    String(area.id) === String(node.id)
                                );
                            });

                            renderSelectedAreas();
                        });

                        $row.append($remove);
                    }

                    $node.append($row);

                    if (node.children.length) {
                        const $children = $('<div class="area-tree-children"></div>');

                        node.children.forEach(function(child) {
                            drawNode(child, $children);
                        });

                        $node.append($children);
                    }

                    $parent.append($node);
                }

                const $tree = $('<div class="area-tree"></div>');

                roots.forEach(function(node) {
                    drawNode(node, $tree);
                });

                $container.append($tree);
            }

            function renderSelectedAreas() {
                $('#selectedAreaCount').text(selectedAreas.length);

                renderAreaTree(
                    $('#selectedAreaList'),
                    selectedAreas,
                    'selected'
                );
            }

            function addSelectedArea(type, id, name, parentId) {
                const existing = selectedAreas.find(function(area) {
                    return area.type === type &&
                        String(area.id) === String(id);
                });

                const cached = areaNodes[type] &&
                    areaNodes[type][String(id)];

                if (existing) return;

                selectedAreas.push({
                    type: type,
                    id: String(id),
                    name: name,
                    parentId: parentId != null ?
                        String(parentId) : (cached && cached.parentId != null ?
                            String(cached.parentId) :
                            null)
                });
            }


            function addSelectedFromSelect(type, selector) {
                const ids = selectedValues(selector);

                if (!ids.length) {
                    showAlert('#marketingAreaMessage', 'আগে অন্তত একটি এলাকা নির্বাচন করো।', 'warning');
                    return;
                }

                ids.forEach(function(id) {
                    const $option = $(selector + ' option').filter(function() {
                        return String(this.value) === id;
                    }).first();

                    if ($option.length) {
                        addSelectedArea(
                            type,
                            id,
                            $option.text(),
                            $option.attr('data-parent-id') || null
                        );
                    }
                });

                $(selector).val([]);
                hideAlert('#marketingAreaMessage');
                renderSelectedAreas();
            }

            $('.add-area-btn').on('click', function() {
                const level = $(this).data('level');

                const map = {
                    division: '#areaDivisions',
                    district: '#areaDistricts',
                    upazila: '#areaUpazilas'
                };

                addSelectedFromSelect(level, map[level]);
            });

            $('#areaCountry').on('change', function() {
                const countryId = $(this).val();

                selectedAreas = [];
                renderSelectedAreas();
                hideAlert('#marketingAreaMessage');
                clearAreaSelects();

                if (!countryId) return;

                fetchDivisions(countryId)
                    .done(function(divisions) {
                        setMultipleOptions('#areaDivisions', divisions);
                    })
                    .fail(function() {
                        showAlert('#marketingAreaMessage', 'Division load করা যায়নি।');
                    });
            });

            $('#areaDivisions').on('change', function() {
                const ids = selectedValues('#areaDivisions');

                resetMultipleSelect('#areaDistricts');
                resetMultipleSelect('#areaUpazilas');

                if (!ids.length) return;

                fetchDistricts(ids)
                    .done(function(districts) {
                        setMultipleOptions('#areaDistricts', districts);
                    })
                    .fail(function() {
                        showAlert('#marketingAreaMessage', 'District load করা যায়নি।');
                    });
            });

            $('#areaDistricts').on('change', function() {
                const ids = selectedValues('#areaDistricts');

                resetMultipleSelect('#areaUpazilas');

                if (!ids.length) return;

                fetchUpazilas(ids)
                    .done(function(upazilas) {
                        setMultipleOptions('#areaUpazilas', upazilas);
                    })
                    .fail(function() {
                        showAlert('#marketingAreaMessage', 'Upazila load করা যায়নি।');
                    });
            });

            /*
             * Restore an existing assignment for editing.
             * Load the full country hierarchy first so existing District-only
             * or Upazila-only scopes can also be restored.
             */
            function restoreAssignment(memberType, memberId) {
                return getJson(urls.assignment, {
                    member_type: memberType,
                    member_id: memberId
                }).then(function(response) {
                    if (!response || !response.success || !response.exists || !response.data) {
                        return;
                    }

                    const data = response.data;
                    const scopes = data.scopes || [];
                    const countryId = data.country_id;

                    if (!countryId) {
                        if (scopes.length) {
                            showAlert(
                                '#marketingAreaMessage',
                                'আগের Assignment-এর Country নির্ধারণ করা যায়নি। Controller response পরীক্ষা করো।',
                                'warning'
                            );
                        }
                        return;
                    }

                    $('#areaCountry').val(String(countryId));

                    return fetchDivisions(countryId).then(function(divisions) {
                        setMultipleOptions('#areaDivisions', divisions);

                        const divisionScopes = scopes.filter(function(s) {
                            return s.scope_type === 'division';
                        });

                        const districtScopes = scopes.filter(function(s) {
                            return s.scope_type === 'district';
                        });

                        const upazilaScopes = scopes.filter(function(s) {
                            return s.scope_type === 'upazila';
                        });

                        divisionScopes.forEach(function(scope) {
                            const option = $('#areaDivisions option[value="' + scope.scope_id + '"]');

                            if (option.length) {
                                addSelectedArea(
                                    'division',
                                    scope.scope_id,
                                    scope.name_en && scope.name_bn ?
                                    scope.name_en + ' / ' + scope.name_bn :
                                    (scope.name_bn || scope.name_en || option.text()),
                                    null
                                );
                            }
                        });

                        $('#areaDivisions').val(
                            divisionScopes.map(function(s) {
                                return String(s.scope_id);
                            })
                        );

                        const allDivisionIds = divisions.map(function(d) {
                            return String(d.id);
                        });

                        return fetchDistricts(allDivisionIds).then(function(districts) {
                            setMultipleOptions('#areaDistricts', districts);

                            districtScopes.forEach(function(scope) {
                                const option = $('#areaDistricts option[value="' + scope.scope_id + '"]');

                                if (option.length) {
                                    addSelectedArea(
                                        'district',
                                        scope.scope_id,
                                        scope.name_en && scope.name_bn ?
                                        scope.name_en + ' / ' + scope.name_bn :
                                        (scope.name_bn || scope.name_en || option.text()),
                                        null
                                    );
                                }
                            });

                            $('#areaDistricts').val(
                                districtScopes.map(function(s) {
                                    return String(s.scope_id);
                                })
                            );

                            const allDistrictIds = districts.map(function(d) {
                                return String(d.id);
                            });

                            return fetchUpazilas(allDistrictIds).then(function(upazilas) {
                                setMultipleOptions('#areaUpazilas', upazilas);

                                upazilaScopes.forEach(function(scope) {
                                    const option = $('#areaUpazilas option[value="' + scope.scope_id + '"]');

                                    if (option.length) {
                                        addSelectedArea(
                                            'upazila',
                                            scope.scope_id,
                                            scope.name_en && scope.name_bn ?
                                            scope.name_en + ' / ' + scope.name_bn :
                                            (scope.name_bn || scope.name_en || option.text()),
                                            null
                                        );
                                    }
                                });

                                $('#areaUpazilas').val(
                                    upazilaScopes.map(function(s) {
                                        return String(s.scope_id);
                                    })
                                );

                                renderSelectedAreas();
                            });
                        });
                    });
                });
            }

            $('.assign-area-btn').on('click', function() {
                const memberType = $(this).data('member-type');
                const memberId = $(this).data('member-id');
                const typeLabel = $(this).data('type-label');
                const memberName = $(this).data('member-name');

                resetAssignmentModal();

                $('#assignmentMemberType').val(memberType);
                $('#assignmentMemberId').val(memberId);
                $('#assignmentMemberInfo').text(typeLabel + ': ' + memberName);

                $('#marketingAreaModal').modal('show');

                restoreAssignment(memberType, memberId).fail(function() {
                    showAlert(
                        '#marketingAreaMessage',
                        'আগের Assignment load করা যায়নি।'
                    );
                });
            });

            /*
             * View assignment details
             */
            let currentViewMember = null;


            function showViewScopes(scopes, countryId) {
                const $content = $('#viewAreaContent').empty();

                if (!scopes || !scopes.length) {
                    renderAreaTree($content, [], 'view');
                    return;
                }

                const divisionIds = Array.from(new Set(
                    scopes
                    .filter(function(s) {
                        return s.scope_type === 'division';
                    })
                    .map(function(s) {
                        return String(s.scope_id);
                    })
                ));

                const ancestorDivisionIds = scopes
                    .filter(function(s) {
                        return s.division_id != null;
                    })
                    .map(function(s) {
                        return String(s.division_id);
                    });

                const allDivisionIds = Array.from(new Set(
                    divisionIds.concat(ancestorDivisionIds)
                ));

                const loadParents = countryId ?
                    fetchDivisions(countryId).then(function(divisions) {
                        cacheAreaItems('division', divisions);

                        return fetchDistricts(allDivisionIds.length ?
                            allDivisionIds :
                            divisions.map(function(d) {
                                return String(d.id);
                            })
                        );
                    }).then(function(districts) {
                        cacheAreaItems('district', districts);
                    }) :
                    $.Deferred().resolve().promise();

                loadParents.done(function() {
                    const treeAreas = scopes.map(function(scope) {
                        const type = scope.scope_type;

                        let parentId = null;

                        if (type === 'district') {
                            parentId = scope.division_id;
                        } else if (type === 'upazila') {
                            parentId = scope.district_id;
                        }

                        const name = scope.name_en && scope.name_bn ?
                            scope.name_en + ' / ' + scope.name_bn :
                            (scope.name_bn || scope.name_en || 'Unknown area');

                        return {
                            type: type,
                            id: String(scope.scope_id),
                            name: name,
                            parentId: parentId == null ? null : String(parentId)
                        };
                    });

                    renderAreaTree($content, treeAreas, 'view');
                }).fail(function() {
                    // Parent data unavailable: still display the saved scopes.
                    const treeAreas = scopes.map(function(scope) {
                        return {
                            type: scope.scope_type,
                            id: String(scope.scope_id),
                            name: scope.name_bn || scope.name_en || 'Unknown area',
                            parentId: scope.scope_type === 'district' ?
                                scope.division_id : (scope.scope_type === 'upazila' ?
                                    scope.district_id :
                                    null)
                        };
                    });

                    renderAreaTree($content, treeAreas, 'view');
                });
            }


            $('.view-area-btn').on('click', function() {
                const memberType = $(this).data('member-type');
                const memberId = $(this).data('member-id');
                const typeLabel = $(this).data('type-label');
                const memberName = $(this).data('member-name');

                currentViewMember = {
                    memberType: memberType,
                    memberId: memberId,
                    typeLabel: typeLabel,
                    memberName: memberName
                };

                $('#viewAreaMemberInfo').text(typeLabel + ': ' + memberName);
                $('#viewAreaContent').html('<div class="text-muted">Loading area...</div>');
                hideAlert('#viewAreaMessage');
                $('#editFromViewBtn').prop('disabled', false);

                $('#viewMarketingAreaModal').modal('show');

                getJson(urls.assignment, {
                    member_type: memberType,
                    member_id: memberId
                }).done(function(response) {
                    if (!response || !response.success) {
                        showAlert(
                            '#viewAreaMessage',
                            response && response.message ? response.message : 'Assignment load করা যায়নি।'
                        );
                        return;
                    }

                    if (!response.exists || !response.data) {
                        showViewScopes([]);
                        return;
                    }

                    showViewScopes(
                        response.data.scopes || [],
                        response.data.country_id
                    );
                }).fail(function(xhr) {
                    showAlert(
                        '#viewAreaMessage',
                        xhr.responseJSON && xhr.responseJSON.message ?
                        xhr.responseJSON.message :
                        'Assignment load করা যায়নি।'
                    );
                });
            });

            $('#editFromViewBtn').on('click', function() {
                if (!currentViewMember) return;

                $('#viewMarketingAreaModal').modal('hide');

                setTimeout(function() {
                    const member = currentViewMember;

                    resetAssignmentModal();

                    $('#assignmentMemberType').val(member.memberType);
                    $('#assignmentMemberId').val(member.memberId);
                    $('#assignmentMemberInfo').text(
                        member.typeLabel + ': ' + member.memberName
                    );

                    $('#marketingAreaModal').modal('show');

                    restoreAssignment(member.memberType, member.memberId)
                        .fail(function() {
                            showAlert('#marketingAreaMessage', 'আগের Assignment load করা যায়নি।');
                        });
                }, 250);
            });

            /*
             * Save assignment
             */
            $('#marketingAreaForm').on('submit', function(event) {
                event.preventDefault();
                hideAlert('#marketingAreaMessage');

                const countryId = $('#areaCountry').val();

                if (!countryId) {
                    showAlert('#marketingAreaMessage', 'Country নির্বাচন করো।');
                    return;
                }

                const data = {
                    member_type: $('#assignmentMemberType').val(),
                    member_id: $('#assignmentMemberId').val(),
                    country_id: countryId,
                    division_ids: [],
                    district_ids: [],
                    upazila_ids: []
                };

                selectedAreas.forEach(function(area) {
                    if (area.type === 'division') {
                        data.division_ids.push(area.id);
                    } else if (area.type === 'district') {
                        data.district_ids.push(area.id);
                    } else if (area.type === 'upazila') {
                        data.upazila_ids.push(area.id);
                    }
                });

                const $button = $('#saveMarketingAreaBtn');

                $button.prop('disabled', true);
                $button.find('.save-btn-text').addClass('d-none');
                $button.find('.save-btn-loading').removeClass('d-none');

                postJson(urls.save, data)
                    .done(function(response) {
                        if (!response || !response.success) {
                            showAlert(
                                '#marketingAreaMessage',
                                response && response.message ?
                                response.message :
                                'Area save করা যায়নি।'
                            );
                            return;
                        }

                        showAlert(
                            '#marketingAreaMessage',
                            response.message || 'Marketing area saved successfully.',
                            'success'
                        );

                        setTimeout(function() {
                            window.location.reload();
                        }, 600);
                    })
                    .fail(function(xhr) {
                        let message = 'Area save করার সময় সমস্যা হয়েছে।';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }

                            if (xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                const firstKey = Object.keys(errors)[0];

                                if (firstKey && errors[firstKey] && errors[firstKey][0]) {
                                    message = errors[firstKey][0];
                                }
                            }
                        }

                        showAlert('#marketingAreaMessage', message);
                    })
                    .always(function() {
                        $button.prop('disabled', false);
                        $button.find('.save-btn-text').removeClass('d-none');
                        $button.find('.save-btn-loading').addClass('d-none');
                    });
            });

            $('#marketingAreaModal').on('hidden.bs.modal', resetAssignmentModal);

            /*
             * Initial setup
             */
            renderSelectedAreas();
            loadFilterDivisions();
            loadMemberAssignments();
        });
    })(jQuery);
</script>
@endpush