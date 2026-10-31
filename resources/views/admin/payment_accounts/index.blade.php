@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Payment Accounts</h4>
                    <a href="{{ route('payment-accounts.create') }}" class="btn btn-primary">
                        Add Account
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Number</th>
                                    <th>Status</th>
                                    <th style="width:150px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($accounts as $account)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $account->name }}</td>

                                    <td>
                                        @if($account->type == 1)
                                            <span class="badge badge-info">Personal</span>
                                        @elseif($account->type == 2)
                                            <span class="badge badge-warning">Agent</span>
                                        @else
                                            <span class="badge badge-success">Merchant</span>
                                        @endif
                                    </td>

                                    <td>{{ $account->number }}</td>

                                    <td>
                                        @if($account->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('payment-accounts.edit', $account->id) }}"
                                            class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('payment-accounts.destroy', $account->id) }}"
                                            method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Are you sure you want to delete this account?');">
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
                                    <td colspan="6" class="text-center">
                                        No payment accounts found.
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