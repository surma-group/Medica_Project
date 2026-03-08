@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Beneficiary</h1>
    </div>  

     <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-has-icon">
            <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
            <div class="alert-body">
                <div class="alert-title">Success</div>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-has-icon">
        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
        <div class="alert-body">
            <div class="alert-title">Whoops! Something went wrong.</div>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('employee.beneficiaries.store') }}">
        @csrf

        @if(isset($beneficiary))
            <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->id }}">
        @endif

        <div class="row">

            <!-- ================= BANK ================= -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Bank</h4>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" class="form-control"
                                value="{{ old('bank_name', $beneficiary->bank_name ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" name="branch_name" class="form-control"
                                value="{{ old('branch_name', $beneficiary->branch_name ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Account Type</label>
                            <select name="account_type" class="form-control">
                                <option value="">Select</option>
                                <option value="1" {{ old('account_type', $beneficiary->account_type ?? '') == 1 ? 'selected' : '' }}>
                                    Savings
                                </option>
                                <option value="2" {{ old('account_type', $beneficiary->account_type ?? '') == 2 ? 'selected' : '' }}>
                                    Current
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" class="form-control"
                                value="{{ old('account_number', $beneficiary->account_number ?? '') }}">
                        </div>

                    </div>
                </div>
            </div>

            <!-- ================= MOBILE BANKING ================= -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Mobile Banking</h4>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>bKash Phone Number</label>
                            <input type="text" name="bkash_number" class="form-control"
                                value="{{ old('bkash_number', $beneficiary->bkash_number ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Rocket Phone Number</label>
                            <input type="text" name="rocket_number" class="form-control"
                                value="{{ old('rocket_number', $beneficiary->rocket_number ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Nagad Phone Number</label>
                            <input type="text" name="nagad_number" class="form-control"
                                value="{{ old('nagad_number', $beneficiary->nagad_number ?? '') }}">
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer text-end mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($beneficiary) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection
