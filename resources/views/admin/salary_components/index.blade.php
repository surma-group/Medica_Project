@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Salary Components</h4>
                    <a href="{{ route('salary_components.create') }}" class="btn btn-primary">Add Salary Component</a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Payment Type</th>
                                    <th>Amount Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($components as $component)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $component->title }}</td>
                                    <td>
                                        @if($component->payment_type == 1)
                                        <div class="badge badge-success">Earning</div>
                                        @else
                                        <div class="badge badge-danger">Deduction</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($component->amount_type == 1)
                                        Flat
                                        @else
                                        Percentage
                                        @endif
                                    </td>
                                    <td>
                                        @if($component->amount_type == 2)
                                        {{ $component->amount }} %
                                        @else
                                        {{ number_format($component->amount, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($component->status)
                                        <div class="badge badge-success">Active</div>
                                        @else
                                        <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('salary_components.edit', $component->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                         @if(SALARY_COMPONENT_PROVIDENT_FUND != $component->id)
                                        <form action="{{ route('salary_components.destroy', $component->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Salary Components found.</td>
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