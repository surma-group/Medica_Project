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

            {{-- My Accounts --}}
            <li class="{{ request()->routeIs('employee.wallet') ? 'active' : '' }}">
                <a href="{{ route('employee.wallet') }}" class="nav-link">
                    <i data-feather="package"></i>
                    <span>My Accounts</span>
                </a>
            </li>

            {{-- Marketing Chain --}}
            <li class="{{ request()->routeIs('employee.marketing.chain') ? 'active' : '' }}">
                <a href="{{ route('employee.marketing.chain') }}" class="nav-link">
                    <i data-feather="git-branch"></i>
                    <span>Marketing Chain</span>
                </a>
            </li>
            {{-- Marketing Area Tree --}}
            <li class="{{ request()->routeIs('employee.marketing.area-tree') ? 'active' : '' }}">
                <a href="{{ route('employee.marketing.area-tree') }}" class="nav-link">
                    <i data-feather="git-branch"></i>
                    <span>Marketing Area</span>
                </a>
            </li>

            {{-- Delivery Management --}}
            @php
            $employee = auth()->user()->employee;
            $deliveryManSetting = \App\Models\Setting::where('slug', 'deliveryMan')
            ->where('status', 1)
            ->first();

            $isDeliveryMan = $employee
            && $deliveryManSetting
            && (int) $employee->designation_id === (int) $deliveryManSetting->value;
            @endphp

            @if ($isDeliveryMan)
            <li class="menu-header">Delivery Management</li>

            <li class="{{ request()->routeIs('employee.delivery-orders.*') ? 'active' : '' }}">
                <a href="{{ route('employee.delivery-orders.index') }}" class="nav-link">
                    <i data-feather="truck"></i>
                    <span>Delivery Orders</span>
                </a>
            </li>
            @endif

        </ul>
    </aside>
</div>