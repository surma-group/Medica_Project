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
            <li class="{{ request()->routeIs('partner.dashboard') ? 'active' : '' }}">
                <a href="{{ route('partner.dashboard') }}" class="nav-link">
                    <i data-feather="monitor"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Orders: Agent only --}}
            @php
            $partner = \App\Models\Partner::where('user_id', Auth::id())->first();
            @endphp

            @if((int) ($partner->type ?? 0) === USER_TYPE_AGENT)
            <li class="{{ request()->routeIs('partner.orders') ? 'active' : '' }}">
                <a href="{{ route('partner.orders') }}" class="nav-link">
                    <i data-feather="shopping-bag"></i>
                    <span class="fw-bold">Orders</span>
                    <span class="badge bg-danger ms-1">Hot</span>
                </a>
            </li>
            @endif

            {{-- Agent Orders: Dealer only --}}
            @if((int) ($partner->type ?? 0) === USER_TYPE_DEALER)
            <li class="{{ request()->routeIs('partner.agent-orders*') ? 'active' : '' }}">
                <a href="{{ route('partner.agent-orders') }}" class="nav-link">
                    <i data-feather="clipboard"></i>
                    <span>Agent Orders</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('partner.incoming-transfers*') ? 'active' : '' }}">
                <a href="{{ route('partner.incoming-transfers') }}" class="nav-link">
                    <i data-feather="truck"></i>
                    <span>Incoming Transfers</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('partner.transfers.outgoing') ? 'active' : '' }}">
                <a href="{{ route('partner.transfers.outgoing') }}" class="nav-link">
                    <i data-feather="send"></i>
                    <span>Send to Agent</span>
                </a>
            </li>
            @endif
            <li class="{{ request()->routeIs('partner.wallet') ? 'active' : '' }}">
                <a href="{{ route('partner.wallet') }}" class="nav-link">
                    <i data-feather="package"></i>
                    <span>My Accounts</span>
                </a>
            </li>
        </ul>
    </aside>
</div>