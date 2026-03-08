<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}">
                <img alt="image" src="{{ asset('admin/assets/img/logo.png') }}" class="header-logo" />
            </a>
        </div>

        <ul class="sidebar-menu">

            <!-- Main -->
            <li class="menu-header">Main</li>

            {{-- Dashboard --}}
            <li class="{{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <a href="{{ route('employee.dashboard') }}" class="nav-link">
                    <i data-feather="monitor"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('employee.salary') ? 'active' : '' }}">
                <a href="{{ route('employee.salary') }}" class="nav-link">
                    <i data-feather="credit-card"></i>
                    <span>Salary</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('employee.wallet') ? 'active' : '' }}">
                <a href="{{ route('employee.wallet') }}" class="nav-link">
                    <i data-feather="package"></i>
                    <span>My Accounts</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('employee.holiday.calendar') ? 'active' : '' }}">
                <a href="{{ route('employee.holiday.calendar') }}" class="nav-link">
                    <i data-feather="calendar"></i>
                    <span>Holiday Calendar</span>
                </a>
            </li>
        </ul>
    </aside>
</div>