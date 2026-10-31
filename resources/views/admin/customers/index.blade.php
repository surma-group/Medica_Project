@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Customers</h4>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        Add Customer
                    </a>
                </div>

                <!-- FILTER START -->
                <div class="card-body pt-3">

                    <form method="GET" class="row mb-3">

                        <div class="col-md-3">
                            <select name="status" class="form-control select2">
                                <option value="">-- All Status --</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary" type="submit">
                                Filter
                            </button>

                            <a href="{{ route('customers.index') }}" class="btn btn-light">
                                Reset
                            </a>
                        </div>

                    </form>

                </div>
                <!-- FILTER END -->

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th style="width:150px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $customer->name }}</td>

                                    <td>{{ $customer->phone }}</td>

                                    <td>{{ $customer->email ?? '-' }}</td>

                                    <td>{{ $customer->address ?? '-' }}</td>

                                    <td>
                                        @if($customer->status == 1)
                                            <div class="badge badge-success">Active</div>
                                        @else
                                            <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                            class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('customers.destroy', $customer->id) }}"
                                            method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Are you sure?');">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No customers found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    {{ $customers->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>
</section>
@endsection