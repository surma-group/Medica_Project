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
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i data-feather="monitor"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Settings -->
            @php
            $settingsActive = request()->routeIs('company.*')
            || request()->routeIs('department.*')
            || request()->routeIs('designation.*')
            || request()->routeIs('branch.*')
            || request()->routeIs('unit.*')
            || request()->routeIs('payment-accounts.*')
            || request()->routeIs('settings.*');
            @endphp

            <li class="dropdown {{ $settingsActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="settings"></i>
                    <span>Settings</span>
                </a>

                <ul class="dropdown-menu" style="{{ $settingsActive ? 'display:block;' : '' }}">
                    <li class="{{ request()->routeIs('company.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.index') }}">Company</a>
                    </li>
                    <li class="{{ request()->routeIs('department.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('department.index') }}">Department</a>
                    </li>
                    <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('settings.index') }}">
                            System Settings
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('designation.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('designation.index') }}">Designation</a>
                    </li>
                    <li class="{{ request()->routeIs('branch.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('branch.index') }}">Branch</a>
                    </li>
                    <li class="{{ request()->routeIs('unit.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('unit.index') }}">Unit</a>
                    </li>
                    <li class="{{ request()->routeIs('payment-accounts.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('payment-accounts.index') }}">
                            Payment Accounts
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Employee -->
            @php
            $employeesActive = request()->routeIs('employees.*');
            @endphp
            <li class="dropdown {{ $employeesActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="users"></i>
                    <span>Employee</span>
                </a>

                <ul class="dropdown-menu" style="{{ $employeesActive ? 'display:block;' : '' }}">
                    <li class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('employees.index') }}">Employees</a>
                    </li>
                </ul>
            </li>
            @php
            $holidayActive = request()->routeIs('holiday.*')
            || request()->routeIs('admin.holiday.calendar');
            @endphp
            <li class="dropdown {{ $holidayActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="users"></i>
                    <span>Holiday</span>
                </a>

                <ul class="dropdown-menu" style="{{ $holidayActive ? 'display:block;' : '' }}">
                    <li class="{{ request()->routeIs('holiday.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('holiday.index') }}">Holiday</a>
                    </li>

                    <li class="{{ request()->routeIs('admin.holiday.calendar') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.holiday.calendar') }}">Holiday Calendar</a>
                    </li>
                </ul>
            </li>

            @php
            $salaryActive = request()->routeIs(
            'salary_components.*',
            'salary_generate.*'
            );
            @endphp

            <li class="dropdown {{ $salaryActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="dollar-sign"></i>
                    <span>Salary</span>
                </a>

                <ul class="dropdown-menu" @if($salaryActive) style="display:block;" @endif>
                    <li class="{{ request()->routeIs('salary_components.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('salary_components.index') }}">Salary Components</a>
                    </li>
                    <li class="{{ request()->routeIs('salary_generate.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('salary_generate.index') }}">
                            Salary Generate
                        </a>
                    </li>
                </ul>
            </li>


            {{-- Marketing --}}
            @php
            $marketingActive = request()->routeIs('marketing.*');
            @endphp

            <li class="dropdown {{ $marketingActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="git-branch"></i>
                    <span>Marketing</span>
                </a>

                <ul class="dropdown-menu" style="{{ $marketingActive ? 'display:block;' : '' }}">

                    {{-- Existing Marketing Chain --}}
                    <li class="{{ request()->routeIs('marketing.chain') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('marketing.chain') }}">
                            Marketing Chain
                        </a>
                    </li>

                    {{-- Commission & Target --}}
                    <li class="{{ request()->routeIs('marketing.commission.target') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('marketing.commission.target') }}">
                            Commission & Target
                        </a>
                    </li>

                    {{-- Marketing Area --}}
                    <li class="{{ request()->routeIs('marketing.area') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('marketing.area') }}">
                            Marketing Area
                        </a>
                    </li>

                </ul>
            </li>



            @php
            $partiesActive = request()->routeIs('suppliers.*')
            || request()->routeIs('customers.*')
            || request()->routeIs('partners.*');
            @endphp

            <li class="dropdown {{ $partiesActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="users"></i>
                    <span>Parties</span>
                </a>

                <ul class="dropdown-menu" style="{{ $partiesActive ? 'display:block;' : '' }}">
                    <li class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('suppliers.index') }}">Suppliers</a>
                    </li>
                    <li class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('customers.index') }}">Customers</a>
                    </li>
                    <!-- Dealers -->
                    <li class="{{ request()->routeIs('partners.dealer.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('partners.dealer.index') }}">
                            Dealers
                        </a>
                    </li>

                    <!-- Agents -->
                    <li class="{{ request()->routeIs('partners.agent.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('partners.agent.index') }}">
                            Agents
                        </a>
                    </li>
                </ul>
            </li>

            @php
            $inventoryActive = request()->routeIs('brands.*')
            || request()->routeIs('categories.*')
            || request()->routeIs('products.*')
            || request()->routeIs('products.request')
            || request()->routeIs('product_company.*')
            || request()->routeIs('product_generic.*')
            || request()->routeIs('product-stock.*');
            @endphp

            <li class="dropdown {{ $inventoryActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="archive"></i>
                    <span>Inventory Management</span>
                </a>

                <ul class="dropdown-menu" style="{{ $inventoryActive ? 'display:block;' : '' }}">
                    <li class="{{ request()->routeIs('product_company.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product_company.index') }}">
                            Product Company
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('product_generic.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product_generic.index') }}">
                            Product Generic
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('brands.index') }}">
                            Brand
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('categories.index') }}">
                            Category
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('products.index') }}">
                            Product
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('products.request') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('em_admin/products/request') }}">
                            Product Request
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('product-stock.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product-stock.index') }}">
                            Product Stock
                        </a>
                    </li>
                </ul>
            </li>
            @php
            $ordersActive =
            request()->routeIs('orders.*') ||
            request()->routeIs('order-requests.*') ||
            request()->routeIs('sales.*') ||
            request()->routeIs('orders.accepted');
            @endphp

            <li class="dropdown {{ $ordersActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="shopping-cart"></i>
                    <span>Orders Management</span>
                </a>

                <ul class="dropdown-menu" style="{{ $ordersActive ? 'display:block;' : '' }}">

                    <li class="{{ request()->routeIs('orders.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('orders.index') }}">
                            All Orders
                        </a>
                    </li>

                    <!-- New Menu -->
                    <li class="{{ request()->routeIs('order-requests.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('order-requests.index') }}">
                            Order Request
                        </a>
                    </li>

                    <!-- NEW: Accepted Orders -->
                    <li class="{{ request()->routeIs('orders.accepted') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('orders.accepted') }}">
                            Accepted Orders
                        </a>
                    </li>

                    {{-- Sales Menu --}}
                    <li class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sales.index') }}">
                            Sales
                        </a>
                    </li>

                </ul>
            </li>

            @php
            $purchaseActive =
            request()->routeIs('purchases.*');
            @endphp

            <li class="dropdown {{ $purchaseActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="truck"></i>
                    <span>Purchases</span>
                </a>

                <ul class="dropdown-menu" style="{{ $purchaseActive ? 'display:block;' : '' }}">

                    <li class="{{ request()->routeIs('purchases.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('purchases.index') }}">
                            Purchases
                        </a>
                    </li>
                </ul>
            </li>

            @php
            $accountsActive = request()->routeIs(
            'add_money.*',
            'withdraw_requests.*',
            'add_money_requests.*',
            'add_employee_balance.*'
            );
            @endphp

            <li class="dropdown {{ $accountsActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="dollar-sign"></i>
                    <span>Accounts</span>
                </a>

                <ul class="dropdown-menu" @if($accountsActive) style="display:block;" @endif>

                    <li class="{{ request()->routeIs('add_money.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('add_money.index') }}">
                            Add Money
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('add_money_requests.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('add_money_requests.index') }}">
                            Add Money Requests
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('add_employee_balance.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('add_employee_balance.index') }}">
                            Add Employee Balance
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('withdraw_requests.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('withdraw_requests.index') }}">
                            Withdraw Requests
                        </a>
                    </li>

                </ul>
            </li>

            @php
            $reportsActive = request()->routeIs('reports.*');
            @endphp

            <li class="dropdown {{ $reportsActive ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i data-feather="file-text"></i>
                    <span>Reports</span>
                </a>

                <ul class="dropdown-menu" @if($reportsActive) style="display:block;" @endif>

                    <li class="{{ request()->routeIs('reports.cash_flow') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('reports.cash_flow') }}">
                            Cash Flow
                        </a>
                    </li>

                    <!-- You can add more report links here in the future -->

                </ul>
            </li>


        </ul>
    </aside>
</div>