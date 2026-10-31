@extends('layouts.admin')

@section('title', 'Add Money Requests')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Add Money Requests</h1>
    </div>

    <div class="section-body">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>All Add Money Requests</h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Transaction ID</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($requests as $key => $request)
                            <tr>
                                <td>{{ $requests->firstItem() + $key }}</td>

                                <td>
                                    {{ $request->user->name ?? 'N/A' }}<br>
                                    <small class="text-muted">
                                        ID: {{ $request->user_id }}
                                    </small>
                                </td>

                                <td>
                                    {{ $request->paymentAccount->name ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $request->paymentAccount->type_name ?? '' }}
                                    </small>
                                </td>

                                <td>
                                    ৳ {{ number_format($request->amount, 2) }}
                                </td>

                                <td>
                                    {{ $request->transaction_id }}
                                </td>

                                <td>
                                    {{ $request->note ?? '—' }}
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

                                    <!-- Approve -->
                                    <button type="button"
                                        class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal"
                                        data-id="{{ $request->id }}">
                                        Approve
                                    </button>

                                    <!-- Reject -->
                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal"
                                        data-id="{{ $request->id }}">
                                        Reject
                                    </button>

                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    No add money requests found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($requests->hasPages())
            <div class="card-footer text-right">
                {{ $requests->links() }}
            </div>
            @endif

        </div>
    </div>
</section>

<!-- =========================
     APPROVE MODAL
========================= -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('add_money_requests.approve') }}">
            @csrf
            <input type="hidden" name="id" id="approveId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <textarea name="approve_note" class="form-control" rows="3" placeholder="Optional note"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================
     REJECT MODAL
========================= -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('add_money_requests.reject') }}">
            @csrf
            <input type="hidden" name="id" id="rejectId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <textarea name="approve_note" class="form-control" rows="3" required placeholder="Rejection reason"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var approveModal = document.getElementById('approveModal');
    approveModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('approveId').value = button.getAttribute('data-id');
    });

    var rejectModal = document.getElementById('rejectModal');
    rejectModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('rejectId').value = button.getAttribute('data-id');
    });

});
</script>
@endpush