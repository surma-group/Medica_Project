@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Withdraw Requests</h4>
                    <a href="{{ route('employee.wallet') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Accountant</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th>Admin Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawRequests as $request)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $request->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($request->method == WITHDRAW_TYPE_CASH)
                                            Cash
                                        @elseif($request->method == WITHDRAW_TYPE_BANK)
                                            Bank
                                        @elseif($request->method == WITHDRAW_TYPE_MOBILE_BANKING)
                                            Mobile Banking
                                        @endif
                                    </td>
                                    <td>{{ number_format($request->amount, 2) }}</td>
                                    <td>
                                        {{ optional(\App\Models\Employee::find($request->data['accountant_id'] ?? null))->user->name ?? 'N/A' }}
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
                                    <td>{{ $request->note ?? 'N/A' }}</td>
                                    <td>{{ $request->admin_note ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No withdraw requests found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection