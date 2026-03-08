@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Add Money Records</h4>
                    <a href="{{ route('add_money.create') }}" class="btn btn-primary">
                        Add Money
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($moneyRecords as $money)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ number_format($money->amount, 2) }}</td>
                                        <td>{{ $money->note ?? '-' }}</td>
                                        <td>{{ $money->creator?->name ?? 'N/A' }}</td>
                                        <td>{{ $money->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No money records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    {{ $moneyRecords->links() }}
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
