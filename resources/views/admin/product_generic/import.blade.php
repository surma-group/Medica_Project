@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Import Product Generics From Excel File</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-has-icon">
            <div class="alert-icon">
                <i class="far fa-lightbulb"></i>
            </div>
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

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">

                <!-- Left Side -->
                <div class="col-md-6 border-right">
                    <h6 class="text-muted mb-3">Steps to Import</h6>

                    <p>Create an Excel file with the following format:</p>

                    <ul>
                        <li><strong>generic_name</strong></li>
                        <li><strong>status</strong> (1 = Active, 0 = Inactive)</li>
                    </ul>

                    <a href="{{ route('product_generic.sample.download') }}" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Download Sample
                    </a>
                </div>

                <!-- Right Side -->
                <div class="col-md-6">
                    <h6 class="text-center mb-4">
                        Upload your .xls / .xlsx / .csv file
                    </h6>

                    <form action="{{ route('product_generic.import.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf

                        <div class="form-group text-center">
                            <input type="file" 
                                   name="file" 
                                   class="form-control" 
                                   accept=".xls,.xlsx,.csv"
                                   required>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-upload"></i> Upload File
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection