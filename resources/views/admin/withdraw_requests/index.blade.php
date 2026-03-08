@extends('layouts.admin')

@section('title', 'Withdraw Requests')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Withdraw Requests</h1>
    </div>

    <div class="section-body">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>All Withdraw Requests</h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawRequests as $key => $request)
                            <tr>
                                <td>{{ $withdrawRequests->firstItem() + $key }}</td>

                                <td>
                                    {{ $request->employee->full_name ?? 'N/A' }}<br>
                                    <small class="text-muted">
                                        ID: {{ $request->employee->employee_code ?? 'N/A' }}
                                    </small>
                                </td>

                                <td>
                                    @php
                                    $methods = [
                                    WITHDRAW_TYPE_CASH => 'Cash',
                                    WITHDRAW_TYPE_BANK => 'Bank',
                                    WITHDRAW_TYPE_MOBILE_BANKING => 'Mobile Banking',
                                    ];
                                    @endphp
                                    {{ $methods[$request->method] ?? 'Unknown' }}
                                </td>

                                <td>
                                    ৳ {{ number_format($request->amount, 2) }}
                                </td>

                                <td>
                                    @if(!empty($request->data))
                                    @php
                                    $details = $request->data;
                                    $accountant = optional(\App\Models\Employee::find($details['accountant_id'] ?? null))->user->name ?? 'N/A';
                                    @endphp

                                    <div><strong>Accountant:</strong> {{ $accountant }}</div>

                                    @if(isset($details['bank_name']))
                                    <div><strong>Bank Name:</strong> {{ $details['bank_name'] }}</div>
                                    <div><strong>Branch:</strong> {{ $details['bank_branch'] ?? 'N/A' }}</div>
                                    <div><strong>Account No:</strong> {{ $details['account_number'] }}</div>
                                    @elseif(isset($details['phone_number']))
                                    @php
                                    $mobileTypes = [
                                    MOBILE_BANKING_TYPE_BKASH => 'bKash',
                                    MOBILE_BANKING_TYPE_NAGAD => 'Nagad',
                                    MOBILE_BANKING_TYPE_ROCKET => 'Rocket',
                                    ];
                                    @endphp
                                    <div>
                                        <strong>Mobile Type:</strong>
                                        {{ $mobileTypes[$details['mobile_banking_type'] ?? 0] ?? 'N/A' }}
                                    </div>
                                    <div><strong>Phone Number:</strong> {{ $details['phone_number'] }}</div>
                                    @endif
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($request->status == 0)
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($request->status == 1)
                                    <span class="badge bg-success">Approved</span>
                                    @elseif($request->status == 2)
                                    <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $request->created_at->format('d M Y') }}<br>
                                    <small class="text-muted">
                                        {{ $request->created_at->format('h:i A') }}
                                    </small>
                                </td>

                                <td>
                                    @if($request->status == 0)
                                    <!-- Approve Button triggers modal -->
                                    <button type="button" class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal"
                                        data-request-id="{{ $request->id }}">
                                        Approve
                                    </button>
                                    <!-- Reject Button triggers modal -->
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal"
                                        data-request-id="{{ $request->id }}">
                                        Reject
                                    </button>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    No withdraw requests found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($withdrawRequests->hasPages())
            <div class="card-footer text-right">
                {{ $withdrawRequests->links() }}
            </div>
            @endif
        </div>

    </div>
</section>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('withdraw.reject.submit') }}">
            @csrf
            <input type="hidden" name="request_id" id="modalRequestId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Withdraw Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectNote" class="form-label">Note</label>
                        <textarea name="note" id="rejectNote" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('withdraw.approve.submit') }}">
            @csrf
            <input type="hidden" name="request_id" id="approveModalRequestId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Withdraw Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approveNote" class="form-label">Note (optional)</label>
                        <textarea name="note" id="approveNote" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var rejectModal = document.getElementById('rejectModal');
    rejectModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var requestId = button.getAttribute('data-request-id');
        var input = rejectModal.querySelector('#modalRequestId');
        input.value = requestId;
    });

    var approveModal = document.getElementById('approveModal');
    approveModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var requestId = button.getAttribute('data-request-id');
        var input = approveModal.querySelector('#approveModalRequestId');
        input.value = requestId;
    });
</script>
@endpush