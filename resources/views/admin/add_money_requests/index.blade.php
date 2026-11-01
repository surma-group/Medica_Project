@extends('layouts.admin')

@section('title', 'Add Money Requests')

@section('content')
<section class="section">

    <div class="section-header">
        <h1>Add Money Requests</h1>
    </div>

    <div class="section-body">

        {{-- Response Message --}}
        <div id="responseMessage"></div>

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

                            <tr id="request-row-{{ $request->id }}">

                                <td>
                                    {{ $requests->firstItem() + $key }}
                                </td>

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

                                {{-- Status --}}
                                <td id="request-status-{{ $request->id }}">

                                    @if($request->status == 0)

                                    <span class="badge bg-warning">
                                        Pending
                                    </span>

                                    @elseif($request->status == 1)

                                    <span class="badge bg-success">
                                        Approved
                                    </span>

                                    @elseif($request->status == 2)

                                    <span class="badge bg-danger">
                                        Rejected
                                    </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $request->created_at->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $request->created_at->format('h:i A') }}
                                    </small>
                                </td>

                                {{-- Actions --}}
                                <td id="request-actions-{{ $request->id }}">

                                    @if($request->status == 0)

                                    <button type="button"
                                        class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal"
                                        data-id="{{ $request->id }}">
                                        Approve
                                    </button>

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


{{-- =========================
     APPROVE MODAL
========================= --}}

<div class="modal fade" id="approveModal" tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
            id="approveForm"
            action="{{ route('add_money_requests.approve') }}">

            @csrf

            <input type="hidden" name="id" id="approveId">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Approve Request</h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <textarea
                        name="approve_note"
                        class="form-control"
                        rows="3"
                        placeholder="Optional note"></textarea>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-success">
                        Approve
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================
     REJECT MODAL
========================= --}}

<div class="modal fade" id="rejectModal" tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
            id="rejectForm"
            action="{{ route('add_money_requests.reject') }}">

            @csrf

            <input type="hidden" name="id" id="rejectId">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">Reject Request</h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <textarea
                        name="approve_note"
                        class="form-control"
                        rows="3"
                        required
                        placeholder="Rejection reason"></textarea>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Reject
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection


{{-- =========================
     JAVASCRIPT
========================= --}}

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // =========================
        // MODAL ID SET
        // =========================

        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');

        approveModal.addEventListener('show.bs.modal', function(event) {

            const button = event.relatedTarget;

            document.getElementById('approveId').value =
                button.getAttribute('data-id');

        });

        rejectModal.addEventListener('show.bs.modal', function(event) {

            const button = event.relatedTarget;

            document.getElementById('rejectId').value =
                button.getAttribute('data-id');

        });


        // =========================
        // SHOW MESSAGE
        // =========================

        function showMessage(message, type) {

            const container = document.getElementById('responseMessage');

            container.innerHTML = '';

            const alert = document.createElement('div');

            alert.className = 'alert alert-' + type + ' alert-dismissible fade show';

            alert.setAttribute('role', 'alert');

            alert.textContent = message;

            const closeButton = document.createElement('button');

            closeButton.type = 'button';
            closeButton.className = 'btn-close';
            closeButton.setAttribute('data-bs-dismiss', 'alert');

            alert.appendChild(closeButton);

            container.appendChild(alert);

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

        }


        // =========================
        // AJAX FORM SUBMIT
        // =========================

        async function handleForm(formId, modalId, status, statusText) {

            const form = document.getElementById(formId);

            form.addEventListener('submit', async function(e) {

                e.preventDefault();

                const submitBtn = form.querySelector('[type="submit"]');

                if (submitBtn.disabled) {
                    return;
                }

                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Processing...';

                try {

                    const response = await fetch(form.action, {

                        method: 'POST',

                        body: new FormData(form),

                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }

                    });

                    const data = await response.json();

                    if (!response.ok) {

                        let errorMessage = data.message || 'Something went wrong';

                        if (data.errors) {

                            errorMessage = Object.values(data.errors)
                                .flat()
                                .join('\n');

                        }

                        throw new Error(errorMessage);

                    }


                    // =========================
                    // GET REQUEST ID
                    // =========================

                    const requestId = form.querySelector('[name="id"]').value;


                    // =========================
                    // UPDATE STATUS
                    // =========================

                    const statusCell = document.getElementById(
                        'request-status-' + requestId
                    );

                    statusCell.innerHTML = '';

                    const badge = document.createElement('span');

                    badge.className = status === 1 ?
                        'badge bg-success' :
                        'badge bg-danger';

                    badge.textContent = statusText;

                    statusCell.appendChild(badge);


                    // =========================
                    // REMOVE ACTION BUTTONS
                    // =========================

                    const actionCell = document.getElementById(
                        'request-actions-' + requestId
                    );

                    actionCell.innerHTML = '<span class="text-muted">-</span>';


                    // =========================
                    // CLOSE MODAL
                    // =========================

                    const modalElement = document.getElementById(modalId);

                    bootstrap.Modal.getOrCreateInstance(modalElement).hide();


                    // =========================
                    // RESET FORM
                    // =========================

                    form.reset();


                    // =========================
                    // SUCCESS MESSAGE
                    // =========================

                    showMessage(
                        data.message || 'Request updated successfully!',
                        'success'
                    );

                } catch (error) {

                    showMessage(
                        error.message || 'Something went wrong!',
                        'danger'
                    );

                } finally {

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;

                }

            });

        }


        // =========================
        // APPROVE
        // =========================

        handleForm(
            'approveForm',
            'approveModal',
            1,
            'Approved'
        );


        // =========================
        // REJECT
        // =========================

        handleForm(
            'rejectForm',
            'rejectModal',
            2,
            'Rejected'
        );

    });
</script>

@endpush