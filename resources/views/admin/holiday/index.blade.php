@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Holidays</h4>
                    <a href="{{ route('holiday.create') }}" class="btn btn-primary">Add Holiday</a>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    @php
                    use Carbon\Carbon;

                    $defaultFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $defaultTo = Carbon::now()->endOfMonth()->format('Y-m-d');
                    @endphp

                    <form action="{{ route('holiday.index') }}" method="GET" class="row g-3 mb-3">
                        <div class="col-md-3">
                            <input type="date" name="from" class="form-control" placeholder="From"
                                value="{{ request('from', $defaultFrom) }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="to" class="form-control" placeholder="To"
                                value="{{ request('to', $defaultTo) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th> <!-- New column -->
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holidays as $holiday)
                                <tr>
                                    <td>{{ ($holidays->currentPage()-1) * $holidays->perPage() + $loop->iteration }}</td>
                                    <td>{{ $holiday->title }}</td> <!-- Show title -->
                                    <td>{{ $holiday->from->format('Y-m-d H:i') }}</td>
                                    <td>{{ $holiday->to->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($holiday->status)
                                        <div class="badge badge-success">Active</div>
                                        @else
                                        <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('holiday.edit', $holiday->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('holiday.destroy', $holiday->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No holidays found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    {{ $holidays->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection