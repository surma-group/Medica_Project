@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Suppliers</h4>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                        Add Supplier
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Mobile</th>
                                    <th>Contact Person</th>
                                    <th>Status</th>
                                    <th style="width:150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $supplier)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->code }}</td>
                                    <td>{{ $supplier->mobile }}</td>
                                    <td>
                                        {{ $supplier->contact_person ?? '-' }}
                                    </td>
                                    <td>
                                        @if($supplier->status == 1)
                                        <div class="badge badge-success">Active</div>
                                        @else
                                        <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                            class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('suppliers.destroy', $supplier->id) }}"
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
                                        No suppliers found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection