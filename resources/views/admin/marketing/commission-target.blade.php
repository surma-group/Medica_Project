@extends('layouts.admin')

@section('title', 'Commission & Target')

@section('content')

<div class="dashboard-title mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h2>Commission & Target</h2>
        <p>Manage sales targets and commissions for employees, dealers and agents.</p>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <h4>Marketing Members</h4>
        </div>

        <div class="card-body">

            {{-- Alert Messages --}}
            <div id="marketingAlert" class="alert d-none" role="alert"></div>

            {{-- Filters --}}
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label for="memberType">Type</label>
                    <select id="memberType" class="form-control">
                        <option value="">All Types</option>
                        <option value="employee">Employee</option>
                        <option value="dealer">Dealer</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>

                <div class="col-md-5 mb-3">
                    <label for="memberSearch">Search</label>
                    <input
                        type="text"
                        id="memberSearch"
                        class="form-control"
                        placeholder="Search name, employee code...">
                </div>

                <div class="col-md-2 mb-3">
                    <button type="button" class="btn btn-primary btn-block" id="searchMember">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>

                <div class="col-md-2 mb-3">
                    <button type="button" class="btn btn-secondary btn-block" id="resetMember">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </button>
                </div>
            </div>

            {{-- Members Table --}}
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Designation</th>
                            <th>Sales Target</th>
                            <th>Commission</th>
                            <th width="190">Action</th>
                        </tr>
                    </thead>

                    <tbody id="marketingMemberTable">
                        @php
                        $serial = 1;
                        @endphp

                        {{-- Employees --}}
                        @foreach($employees as $employee)
                        <tr
                            data-type="employee"
                            data-member-type="{{ USER_TYPE_EMPLOYEE }}"
                            data-id="{{ $employee->id }}"
                            data-search="{{ strtolower(($employee->full_name ?? '') . ' ' . ($employee->employee_code ?? '') . ' ' . (optional($employee->designation)->name ?? '')) }}">
                            <td>{{ $serial++ }}</td>

                            <td>
                                <strong>{{ $employee->full_name ?? '-' }}</strong>

                                @if(!empty($employee->employee_code))
                                <div class="text-muted small">
                                    Employee Code: {{ $employee->employee_code }}
                                </div>
                                @endif
                            </td>

                            <td><span class="badge badge-primary">Employee</span></td>

                            <td>{{ optional($employee->designation)->name ?? '-' }}</td>

                            <td class="target-display">
                                <span class="text-muted">Not Set</span>
                            </td>

                            <td class="commission-display">
                                <span class="text-muted">Not Set</span>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-info set-target"
                                    data-id="{{ $employee->id }}"
                                    data-member-type="{{ USER_TYPE_EMPLOYEE }}"
                                    data-type="employee"
                                    data-name="{{ $employee->full_name }}">
                                    <i class="fas fa-bullseye mr-1"></i> Target
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-success set-commission"
                                    data-id="{{ $employee->id }}"
                                    data-member-type="{{ USER_TYPE_EMPLOYEE }}"
                                    data-type="employee"
                                    data-name="{{ $employee->full_name }}">
                                    <i class="fas fa-percent mr-1"></i> Commission
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        {{-- Dealers --}}
                        @foreach($dealers as $dealer)
                        <tr
                            data-type="dealer"
                            data-member-type="{{ USER_TYPE_DEALER }}"
                            data-id="{{ $dealer->id }}"
                            data-search="{{ strtolower(($dealer->name ?? '') . ' ' . ($dealer->shop_name ?? '')) }}">
                            <td>{{ $serial++ }}</td>

                            <td>
                                <strong>{{ $dealer->name ?? '-' }}</strong>

                                @if(!empty($dealer->shop_name))
                                <div class="text-muted small">
                                    Shop: {{ $dealer->shop_name }}
                                </div>
                                @endif
                            </td>

                            <td><span class="badge badge-warning text-dark">Dealer</span></td>
                            <td>Dealer</td>

                            <td class="target-display">
                                <span class="text-muted">Not Set</span>
                            </td>

                            <td class="commission-display">
                                <span class="text-muted">Not Set</span>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-info set-target"
                                    data-id="{{ $dealer->id }}"
                                    data-member-type="{{ USER_TYPE_DEALER }}"
                                    data-type="dealer"
                                    data-name="{{ $dealer->name }}">
                                    <i class="fas fa-bullseye mr-1"></i> Target
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-success set-commission"
                                    data-id="{{ $dealer->id }}"
                                    data-member-type="{{ USER_TYPE_DEALER }}"
                                    data-type="dealer"
                                    data-name="{{ $dealer->name }}">
                                    <i class="fas fa-percent mr-1"></i> Commission
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        {{-- Agents --}}
                        @foreach($agents as $agent)
                        <tr
                            data-type="agent"
                            data-member-type="{{ USER_TYPE_AGENT }}"
                            data-id="{{ $agent->id }}"
                            data-search="{{ strtolower(($agent->name ?? '') . ' ' . ($agent->shop_name ?? '')) }}">
                            <td>{{ $serial++ }}</td>

                            <td>
                                <strong>{{ $agent->name ?? '-' }}</strong>

                                @if(!empty($agent->shop_name))
                                <div class="text-muted small">
                                    Shop: {{ $agent->shop_name }}
                                </div>
                                @endif
                            </td>

                            <td><span class="badge badge-info">Agent</span></td>
                            <td>Agent</td>

                            <td class="target-display">
                                <span class="text-muted">Not Set</span>
                            </td>

                            <td class="commission-display">
                                <span class="text-muted">Not Set</span>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-info set-target"
                                    data-id="{{ $agent->id }}"
                                    data-member-type="{{ USER_TYPE_AGENT }}"
                                    data-type="agent"
                                    data-name="{{ $agent->name }}">
                                    <i class="fas fa-bullseye mr-1"></i> Target
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-success set-commission"
                                    data-id="{{ $agent->id }}"
                                    data-member-type="{{ USER_TYPE_AGENT }}"
                                    data-type="agent"
                                    data-name="{{ $agent->name }}">
                                    <i class="fas fa-percent mr-1"></i> Commission
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        @if($employees->isEmpty() && $dealers->isEmpty() && $agents->isEmpty())
                        <tr id="noMarketingMember">
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <div class="text-muted">
                                    No active marketing members found.
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>


{{-- Target Modal --}}
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bullseye mr-1"></i> Set Sales Target
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="targetMemberName">Member</label>
                    <input type="text" class="form-control" id="targetMemberName" readonly>
                </div>

                <div class="form-group">
                    <label for="targetPeriod">Target Period</label>
                    <select class="form-control" id="targetPeriod" required>
                        <option value="1">Daily</option>
                        <option value="2">Weekly</option>
                        <option value="3" selected>Monthly</option>
                        <option value="4">Yearly</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="targetType">Target Type</label>
                    <select class="form-control" id="targetType" required>
                        <option value="1">Flat Amount</option>
                        <option value="2">Percentage</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="targetAmount">Target Amount</label>
                    <input
                        type="number"
                        class="form-control"
                        id="targetAmount"
                        min="0"
                        max="9999999999999.99"
                        step="0.01"
                        placeholder="Enter target amount"
                        required>
                    <small class="form-text text-muted">
                        Percentage type uses a percentage value; flat type uses a fixed amount.
                    </small>
                </div>

                <div class="text-danger small d-none" id="targetError"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>

                <button type="button" class="btn btn-primary" id="saveTargetBtn">
                    <i class="fas fa-save mr-1"></i> Save Target
                </button>
            </div>

        </div>
    </div>
</div>


{{-- Commission Modal --}}
<div class="modal fade" id="commissionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-percent mr-1"></i> Set Commission
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="commissionMemberName">Member</label>
                    <input type="text" class="form-control" id="commissionMemberName" readonly>
                </div>

                <div class="form-group">
                    <label for="commissionType">Commission Type</label>
                    <select class="form-control" id="commissionType" required>
                        <option value="1">Flat Amount</option>
                        <option value="2">Percentage</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="commissionAmount">Commission Amount</label>
                    <input
                        type="number"
                        class="form-control"
                        id="commissionAmount"
                        min="0"
                        max="9999999999999.99"
                        step="0.01"
                        placeholder="Enter commission amount"
                        required>
                </div>

                <div class="text-danger small d-none" id="commissionError"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>

                <button type="button" class="btn btn-success" id="saveCommissionBtn">
                    <i class="fas fa-save mr-1"></i> Save Commission
                </button>
            </div>

        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
    $(document).ready(function() {

        let selectedTargetMember = null;
        let selectedCommissionMember = null;

        const targetSaveUrl = @json(route('marketing.commission.target.save'));
        const commissionSaveUrl = @json(route('marketing.commission.target.commission.save'));
        
        const csrfToken = @json(csrf_token());

        const periodNames = {
            1: 'Daily',
            2: 'Weekly',
            3: 'Monthly',
            4: 'Yearly'
        };

        function showAlert(type, message) {
            $('#marketingAlert')
                .removeClass('d-none alert-success alert-danger alert-warning')
                .addClass('alert-' + type)
                .text(message);

            $('html, body').animate({
                scrollTop: $('#marketingAlert').offset().top - 100
            }, 200);
        }

        function showModalError(selector, message) {
            $(selector).removeClass('d-none').text(message);
        }

        function clearModalError(selector) {
            $(selector).addClass('d-none').text('');
        }

        function setButtonLoading(buttonSelector, loading, originalText) {
            const button = $(buttonSelector);

            if (loading) {
                button.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...'
                );
            } else {
                button.prop('disabled', false).html(originalText);
            }
        }

        function formatAmount(amount) {
            return Number(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Open Target Modal
        |--------------------------------------------------------------------------
        */

        $(document).on('click', '.set-target', function() {
            selectedTargetMember = {
                id: Number($(this).data('id')),
                memberType: Number($(this).data('member-type')),
                type: String($(this).data('type')),
                name: String($(this).data('name') || '')
            };

            $('#targetMemberName').val(selectedTargetMember.name);
            $('#targetPeriod').val('3');
            $('#targetType').val('1');
            $('#targetAmount').val('');
            clearModalError('#targetError');

            $('#targetModal').modal('show');
        });

        /*
        |--------------------------------------------------------------------------
        | Open Commission Modal
        |--------------------------------------------------------------------------
        */

        $(document).on('click', '.set-commission', function() {
            selectedCommissionMember = {
                id: Number($(this).data('id')),
                memberType: Number($(this).data('member-type')),
                type: String($(this).data('type')),
                name: String($(this).data('name') || '')
            };

            $('#commissionMemberName').val(selectedCommissionMember.name);
            $('#commissionType').val('1');
            $('#commissionAmount').val('');
            clearModalError('#commissionError');

            $('#commissionModal').modal('show');
        });

        /*
        |--------------------------------------------------------------------------
        | Save Target
        |--------------------------------------------------------------------------
        */

        $('#saveTargetBtn').on('click', function() {
            clearModalError('#targetError');

            if (!selectedTargetMember) {
                showModalError('#targetError', 'Please select a marketing member.');
                return;
            }

            const amount = $('#targetAmount').val();

            if (amount === '' || !Number.isFinite(Number(amount)) || Number(amount) < 0) {
                showModalError('#targetError', 'Please enter a valid target amount.');
                return;
            }

            const periodType = Number($('#targetPeriod').val());
            const targetType = Number($('#targetType').val());

            if (![1, 2, 3, 4].includes(periodType)) {
                showModalError('#targetError', 'Please select a valid target period.');
                return;
            }

            if (![1, 2].includes(targetType)) {
                showModalError('#targetError', 'Please select a valid target type.');
                return;
            }

            const payload = {
                _token: csrfToken,
                member_type: selectedTargetMember.memberType,
                member_id: selectedTargetMember.id,
                period_type: periodType,
                target_type: targetType,
                target_amount: amount
            };

            setButtonLoading(
                '#saveTargetBtn',
                true,
                '<i class="fas fa-save mr-1"></i> Save Target'
            );

            $.ajax({
                url: targetSaveUrl,
                type: 'POST',
                data: payload,
                dataType: 'json',

                success: function(response) {
                    if (!response.success) {
                        showModalError(
                            '#targetError',
                            response.message || 'Unable to save target.'
                        );
                        return;
                    }

                    const row = $('#marketingMemberTable tr[data-type]')
                        .filter(function() {
                            return Number($(this).data('id')) === selectedTargetMember.id &&
                                Number($(this).data('member-type')) === selectedTargetMember.memberType;
                        });

                    const targetLabel = targetType === 2 ?
                        formatAmount(amount) + '%' :
                        '৳' + formatAmount(amount);

                    row.find('.target-display').text(
                        periodNames[periodType] + ': ' + targetLabel
                    );

                    $('#targetModal').modal('hide');
                    showAlert('success', response.message || 'Sales target saved successfully.');
                },

                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    const message = response.message || 'An error occurred while saving the target.';
                    showModalError('#targetError', message);
                },

                complete: function() {
                    setButtonLoading(
                        '#saveTargetBtn',
                        false,
                        '<i class="fas fa-save mr-1"></i> Save Target'
                    );
                }
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Save Commission
        |--------------------------------------------------------------------------
        */

        $('#saveCommissionBtn').on('click', function() {
            clearModalError('#commissionError');

            if (!selectedCommissionMember) {
                showModalError('#commissionError', 'Please select a marketing member.');
                return;
            }

            const amount = $('#commissionAmount').val();

            if (amount === '' || !Number.isFinite(Number(amount)) || Number(amount) < 0) {
                showModalError('#commissionError', 'Please enter a valid commission amount.');
                return;
            }

            const commissionType = Number($('#commissionType').val());

            if (![1, 2].includes(commissionType)) {
                showModalError('#commissionError', 'Please select a valid commission type.');
                return;
            }

            const payload = {
                _token: csrfToken,
                member_type: selectedCommissionMember.memberType,
                member_id: selectedCommissionMember.id,
                commission_type: commissionType,
                commission_amount: amount
            };

            setButtonLoading(
                '#saveCommissionBtn',
                true,
                '<i class="fas fa-save mr-1"></i> Save Commission'
            );

            $.ajax({
                url: commissionSaveUrl,
                type: 'POST',
                data: payload,
                dataType: 'json',

                success: function(response) {
                    if (!response.success) {
                        showModalError(
                            '#commissionError',
                            response.message || 'Unable to save commission.'
                        );
                        return;
                    }

                    const row = $('#marketingMemberTable tr[data-type]')
                        .filter(function() {
                            return Number($(this).data('id')) === selectedCommissionMember.id &&
                                Number($(this).data('member-type')) === selectedCommissionMember.memberType;
                        });

                    const commissionLabel = commissionType === 2 ?
                        formatAmount(amount) + '%' :
                        '৳' + formatAmount(amount);

                    row.find('.commission-display').text(commissionLabel);

                    $('#commissionModal').modal('hide');
                    showAlert('success', response.message || 'Commission saved successfully.');
                },

                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    const message = response.message || 'An error occurred while saving the commission.';
                    showModalError('#commissionError', message);
                },

                complete: function() {
                    setButtonLoading(
                        '#saveCommissionBtn',
                        false,
                        '<i class="fas fa-save mr-1"></i> Save Commission'
                    );
                }
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Search & Filter
        |--------------------------------------------------------------------------
        */

        function filterMembers() {
            const type = String($('#memberType').val() || '').toLowerCase();
            const search = String($('#memberSearch').val() || '').trim().toLowerCase();

            let visibleCount = 0;

            $('#marketingMemberTable tr[data-type]').each(function() {
                const row = $(this);
                const rowType = String(row.data('type')).toLowerCase();
                const rowSearch = String(row.data('search') || '').toLowerCase();

                const typeMatch = !type || rowType === type;
                const searchMatch = !search || rowSearch.includes(search);
                const visible = typeMatch && searchMatch;

                row.toggle(visible);

                if (visible) {
                    visibleCount++;
                }
            });

            $('#noMarketingMember').remove();

            if (visibleCount === 0) {
                $('#marketingMemberTable').append(`
                <tr id="noMarketingMember">
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-2"></i>
                        <div class="text-muted">
                            No matching marketing member found.
                        </div>
                    </td>
                </tr>
            `);
            }
        }

        $('#searchMember').on('click', filterMembers);

        $('#memberSearch').on('keyup', function(event) {
            if (event.key === 'Enter') {
                filterMembers();
            }
        });

        $('#memberType').on('change', filterMembers);

        /*
        |--------------------------------------------------------------------------
        | Reset Filters
        |--------------------------------------------------------------------------
        */

        $('#resetMember').on('click', function() {
            $('#memberType').val('');
            $('#memberSearch').val('');
            $('#noMarketingMember').remove();
            $('#marketingMemberTable tr[data-type]').show();
        });

    });
</script>
@endpush