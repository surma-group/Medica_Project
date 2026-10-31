@extends('layouts.admin')

@section('title', 'Withdraw Requests')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Withdraw Requests</h1>
    </div>

    <div class="section-body">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>All Withdraw Requests</h4>
                <span class="badge bg-primary">
                    {{ $withdrawRequests->total() }} Requests
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Requested By</th>
                                <th>Type</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Withdrawal Details</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($withdrawRequests as $key => $withdraw)
                            @php
                            $reference = $withdraw->reference;

                            $referenceType = (string) $withdraw->reference_type;

                            $isEmployee = $referenceType === (string) USER_TYPE_EMPLOYEE;

                            $isPartner = in_array(
                            $referenceType,
                            [
                            (string) USER_TYPE_DEALER,
                            (string) USER_TYPE_AGENT,
                            ],
                            true
                            );

                            $personName = 'N/A';
                            $personCode = 'N/A';
                            $personType = 'Unknown';

                            if ($reference) {
                            if ($isEmployee) {
                            $personName = $reference->full_name ?? 'N/A';
                            $personCode = $reference->employee_code
                            ?? $reference->account_number
                            ?? 'N/A';

                            $personType = 'Employee';
                            } elseif ($isPartner) {
                            $personName = $reference->full_name
                            ?? $reference->name
                            ?? 'N/A';

                            $personCode = $reference->account_number
                            ?? $reference->id
                            ?? 'N/A';

                            $personType = $referenceType === (string) USER_TYPE_DEALER
                            ? 'Dealer'
                            : 'Agent';
                            }
                            }

                            $methods = [
                            WITHDRAW_TYPE_CASH => 'Cash',
                            WITHDRAW_TYPE_BANK => 'Bank',
                            WITHDRAW_TYPE_MOBILE_BANKING => 'Mobile Banking',
                            ];

                            $details = is_array($withdraw->data)
                            ? $withdraw->data
                            : [];

                            $mobileTypes = [
                            MOBILE_BANKING_TYPE_BKASH => 'bKash',
                            MOBILE_BANKING_TYPE_NAGAD => 'Nagad',
                            MOBILE_BANKING_TYPE_ROCKET => 'Rocket',
                            ];
                            @endphp

                            <tr>
                                <td>{{ $withdrawRequests->firstItem() + $key }}</td>

                                <td>
                                    <strong>{{ $personName }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        ID: {{ $personCode }}
                                    </small>
                                </td>

                                <td>
                                    @if($isEmployee)
                                    <span class="badge bg-info">Employee</span>
                                    @elseif($personType === 'Dealer')
                                    <span class="badge bg-primary">Dealer</span>
                                    @elseif($personType === 'Agent')
                                    <span class="badge bg-secondary">Agent</span>
                                    @else
                                    <span class="badge bg-dark">Unknown</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $methods[$withdraw->method] ?? $withdraw->method }}
                                </td>

                                <td>
                                    <strong>
                                        ৳ {{ number_format((float) $withdraw->amount, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    @if(!empty($details))
                                    @if(!empty($details['accountant_id']))
                                    @php
                                    $accountant = \App\Models\Employee::find($details['accountant_id']);
                                    @endphp

                                    <div>
                                        <strong>Accountant Account No:</strong>
                                        {{ $accountant->account_number ?? 'N/A' }}
                                    </div>
                                    @endif

                                    @if(!empty($details['bank_name']))
                                    <div>
                                        <strong>Bank:</strong>
                                        {{ $details['bank_name'] }}
                                    </div>

                                    <div>
                                        <strong>Branch:</strong>
                                        {{ $details['bank_branch'] ?? 'N/A' }}
                                    </div>

                                    <div>
                                        <strong>Account No:</strong>
                                        {{ $details['account_number'] ?? 'N/A' }}
                                    </div>
                                    @elseif(!empty($details['phone_number']))
                                    <div>
                                        <strong>Mobile Type:</strong>
                                        {{ $mobileTypes[$details['mobile_banking_type'] ?? 0] ?? 'N/A' }}
                                    </div>

                                    <div>
                                        <strong>Phone:</strong>
                                        {{ $details['phone_number'] }}
                                    </div>
                                    @else
                                    <span class="text-muted">No additional details</span>
                                    @endif
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if((int) $withdraw->status === 0)
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif((int) $withdraw->status === 1)
                                    <span class="badge bg-success">Approved</span>
                                    @elseif((int) $withdraw->status === 2)
                                    <span class="badge bg-danger">Rejected</span>
                                    @else
                                    <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $withdraw->created_at?->format('d M Y') ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $withdraw->created_at?->format('h:i A') ?? '' }}
                                    </small>
                                </td>

                                <td>
                                    @if((int) $withdraw->status === 0)
                                    <div class="d-flex gap-1">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal"
                                            data-request-id="{{ $withdraw->id }}"
                                            data-request-name="{{ $personName }}"
                                            data-request-amount="{{ number_format((float) $withdraw->amount, 2, '.', '') }}">
                                            Approve
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal"
                                            data-request-id="{{ $withdraw->id }}"
                                            data-request-name="{{ $personName }}">
                                            Reject
                                        </button>
                                    </div>
                                    @else
                                    <span class="text-muted">Processed</span>

                                    @if($withdraw->admin_note)
                                    <br>
                                    <small
                                        class="text-muted"
                                        title="{{ $withdraw->admin_note }}">
                                        Admin note available
                                    </small>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    No withdrawal requests found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($withdrawRequests->hasPages())
            <div class="card-footer">
                {{ $withdrawRequests->links() }}
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1"
    aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('withdraw.reject.submit') }}">
            @csrf

            <input type="hidden" name="request_id" id="rejectRequestId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">
                        Reject Withdrawal Request
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Reject request for
                        <strong id="rejectPersonName">—</strong>?
                    </p>

                    <div class="mb-3">
                        <label for="rejectNote" class="form-label">
                            Rejection Reason <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="note"
                            id="rejectNote"
                            class="form-control"
                            rows="3"
                            maxlength="500"
                            required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-danger">
                        Confirm Reject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1"
    aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('withdraw.approve.submit') }}">
            @csrf

            <input type="hidden" name="request_id" id="approveRequestId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">
                        Approve Withdrawal Request
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Approve withdrawal for
                        <strong id="approvePersonName">—</strong>?
                    </p>

                    <p>
                        Amount:
                        <strong id="approveAmount">৳ 0.00</strong>
                    </p>

                    <div class="mb-3">
                        <label for="approveNote" class="form-label">
                            Admin Note (optional)
                        </label>

                        <textarea
                            name="note"
                            id="approveNote"
                            class="form-control"
                            rows="3"
                            maxlength="500"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-success">
                        Confirm Approve
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rejectModal = document.getElementById('rejectModal');
        const approveModal = document.getElementById('approveModal');

        if (rejectModal) {
            rejectModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                if (!button) return;

                document.getElementById('rejectRequestId').value =
                    button.getAttribute('data-request-id') || '';

                document.getElementById('rejectPersonName').textContent =
                    button.getAttribute('data-request-name') || 'N/A';

                document.getElementById('rejectNote').value = '';
            });
        }

        if (approveModal) {
            approveModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                if (!button) return;

                document.getElementById('approveRequestId').value =
                    button.getAttribute('data-request-id') || '';

                document.getElementById('approvePersonName').textContent =
                    button.getAttribute('data-request-name') || 'N/A';

                document.getElementById('approveAmount').textContent =
                    '৳ ' + (button.getAttribute('data-request-amount') || '0.00');

                document.getElementById('approveNote').value = '';
            });
        }
    });
</script>
@endpush