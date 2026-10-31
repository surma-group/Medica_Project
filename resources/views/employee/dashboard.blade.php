@extends('layouts.employee')

@section('content')

<div class="dashboard-title mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h2>Employee Dashboard</h2>
        <p>Welcome, {{ Auth::user()->name ?? 'Employee' }}!</p>
    </div>

    <div>
        <a href="#" class="btn btn-primary">
            <i class="fas fa-sign-in-alt mr-1"></i>
            Check In
        </a>
    </div>
</div>

<section class="section">

    {{-- Statistics --}}
    <div class="row">

        {{-- Attendance --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-3">
            <div class="card h-100">
                <div class="card-statistic-4 h-100">
                    <div class="row h-100">

                        <div class="col-12 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Attendance</h5>

                                <h2 class="mb-3 font-18">
                                    22
                                </h2>

                                <p class="mb-0">
                                    <span class="col-green">Present</span>
                                    This Month
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        {{-- Leave --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-3">
            <div class="card h-100">
                <div class="card-statistic-4 h-100">
                    <div class="row h-100">

                        <div class="col-12 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Leave Balance</h5>

                                <h2 class="mb-3 font-18">
                                    12
                                </h2>

                                <p class="mb-0">
                                    <span class="col-orange">Days</span>
                                    Remaining
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        {{-- Tasks --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-3">
            <div class="card h-100">
                <div class="card-statistic-4 h-100">
                    <div class="row h-100">

                        <div class="col-12 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">My Tasks</h5>

                                <h2 class="mb-3 font-18">
                                    8
                                </h2>

                                <p class="mb-0">
                                    <span class="col-green">5</span>
                                    Completed
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        {{-- Salary --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-3">
            <div class="card h-100">
                <div class="card-statistic-4 h-100">
                    <div class="row h-100">

                        <div class="col-12 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Salary</h5>

                                <h2 class="mb-3 font-18">
                                    ৳48,697
                                </h2>

                                <p class="mb-0">
                                    <span class="col-green">Current</span>
                                    Month
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Employee Information --}}
    <div class="row">

        <div class="col-lg-8">
            <div class="card">

                <div class="card-header">
                    <h4>My Information</h4>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <strong>Name</strong>
                            <p class="mb-0">
                                {{ Auth::user()->name ?? 'Employee' }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Email</strong>
                            <p class="mb-0">
                                {{ Auth::user()->email ?? '-' }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Employee Code</strong>
                            <p class="mb-0">
                                {{ Auth::user()->employee->employee_code ?? '-' }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Designation</strong>
                            <p class="mb-0">
                                {{ Auth::user()->employee->designation->name ?? '-' }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>
        </div>


        {{-- Quick Actions --}}
        <div class="col-lg-4">

            <div class="card">

                <div class="card-header">
                    <h4>Quick Actions</h4>
                </div>

                <div class="card-body">

                    <div class="mb-2">
                        <a href="#" class="btn btn-primary btn-block">
                            <i class="fas fa-user-clock mr-1"></i>
                            Attendance
                        </a>
                    </div>

                    <div class="mb-2">
                        <a href="#" class="btn btn-info btn-block">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Apply Leave
                        </a>
                    </div>

                    <div class="mb-2">
                        <a href="#" class="btn btn-success btn-block">
                            <i class="fas fa-money-check-alt mr-1"></i>
                            Salary Details
                        </a>
                    </div>

                    <div>
                        <a href="#" class="btn btn-secondary btn-block">
                            <i class="fas fa-user mr-1"></i>
                            My Profile
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection