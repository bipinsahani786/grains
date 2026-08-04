<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/" class="b-brand d-flex align-items-center">
                @if(isset($platformLogo) && $platformLogo)
                    <img src="{{ asset('storage/' . $platformLogo) }}" alt="{{ $platformBrand ?? 'Platform' }}" class="logo logo-lg" style="max-height: 40px;" />
                    <img src="{{ asset('storage/' . $platformLogo) }}" alt="{{ $platformBrand ?? 'Platform' }}" class="logo logo-sm" style="max-height: 30px;" />
                @else
                    <h3 class="logo logo-lg text-primary fw-bolder mb-0">{{ $platformBrand ?? 'Platform' }}</h3>
                    <h3 class="logo logo-sm text-primary fw-bolder mb-0">{{ substr($platformBrand ?? 'P', 0, 1) }}</h3>
                @endif
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">

                @if(is_null(auth()->user()->company_id))
                {{-- ========================================== --}}
                {{-- SUPER ADMIN SIDEBAR                        --}}
                {{-- ========================================== --}}
                <li class="nxl-item nxl-caption">
                    <label>Super Admin</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('superadmin.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Management</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('superadmin.companies.*') ? 'active' : '' }}">
                    <a href="{{ route('superadmin.companies.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                        <span class="nxl-mtext">Tenants (Companies)</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                    <a href="{{ route('superadmin.plans.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Subscription Plans</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('superadmin.users.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Platform Users</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('superadmin.invoices.*') ? 'active' : '' }}">
                    <a href="{{ route('superadmin.invoices.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-file-text"></i></span>
                        <span class="nxl-mtext">SaaS Invoices</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Configuration</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('superadmin.settings.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">System Settings</span>
                    </a>
                </li>

                @else
                {{-- ========================================== --}}
                {{-- BUSINESS ADMIN / STAFF SIDEBAR             --}}
                {{-- ========================================== --}}
                <li class="nxl-item nxl-caption">
                    <label>Main</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('business.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Masters Data</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.party-types.*') ? 'active' : '' }}">
                    <a href="{{ route('business.party-types.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-tag"></i></span>
                        <span class="nxl-mtext">Party Types</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.parties.*') ? 'active' : '' }}">
                    <a href="{{ route('business.parties.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Parties</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.financials.commissions.*') ? 'active' : '' }}">
                    <a href="{{ route('business.financials.commissions.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user-check"></i></span>
                        <span class="nxl-mtext">Brokers</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.grains.*') ? 'active' : '' }}">
                    <a href="{{ route('business.grains.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-hexagon"></i></span>
                        <span class="nxl-mtext">Grains</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.godowns.*') ? 'active' : '' }}">
                    <a href="{{ route('business.godowns.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Godowns</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Trading</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.purchases.*') ? 'active' : '' }}">
                    <a href="{{ route('business.purchases.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-download"></i></span>
                        <span class="nxl-mtext">Purchases</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.sales.*') ? 'active' : '' }}">
                    <a href="{{ route('business.sales.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-upload"></i></span>
                        <span class="nxl-mtext">Sales</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Inventory Management</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.inventory.stocks.*') ? 'active' : '' }}">
                    <a href="{{ route('business.inventory.stocks.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-layers"></i></span>
                        <span class="nxl-mtext">Grain Stocks</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.inventory.lots.*') ? 'active' : '' }}">
                    <a href="{{ route('business.inventory.lots.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-grid"></i></span>
                        <span class="nxl-mtext">Lots (FIFO)</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.inventory.logs.*') ? 'active' : '' }}">
                    <a href="{{ route('business.inventory.logs.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-clock"></i></span>
                        <span class="nxl-mtext">Inventory Logs</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.inventory.adjustments.*') ? 'active' : '' }}">
                    <a href="{{ route('business.inventory.adjustments.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-sliders"></i></span>
                        <span class="nxl-mtext">Stock Adjustments</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Financials</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.financials.ledger.*') ? 'active' : '' }}">
                    <a href="{{ route('business.financials.ledger.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-book"></i></span>
                        <span class="nxl-mtext">Ledger Entries</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.expenses.*') ? 'active' : '' }}">
                    <a href="{{ route('business.expenses.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Expenses</span>
                    </a>
                </li>


                <li class="nxl-item nxl-caption">
                    <label>Reports</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.reports.purchases') ? 'active' : '' }}">
                    <a href="{{ route('business.reports.purchases') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-file-minus"></i></span>
                        <span class="nxl-mtext">Purchase Report</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.reports.sales') ? 'active' : '' }}">
                    <a href="{{ route('business.reports.sales') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-file-plus"></i></span>
                        <span class="nxl-mtext">Sales Report</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.reports.current-stock') ? 'active' : '' }}">
                    <a href="{{ route('business.reports.current-stock') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-box"></i></span>
                        <span class="nxl-mtext">Stock Report</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.reports.party-ledger') ? 'active' : '' }}">
                    <a href="{{ route('business.reports.party-ledger') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-file-text"></i></span>
                        <span class="nxl-mtext">Party Ledger Report</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.reports.profit') ? 'active' : '' }}">
                    <a href="{{ route('business.reports.profit') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-trending-up"></i></span>
                        <span class="nxl-mtext">Profit Report</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.reports.expenses') ? 'active' : '' }}">
                    <a href="{{ route('business.reports.expenses') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-trending-down"></i></span>
                        <span class="nxl-mtext">Expense Report</span>
                    </a>
                </li>
                @endif

                <li class="nxl-item nxl-caption">
                    <label>Configuration</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('business.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('business.settings.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">Settings</span>
                    </a>
                </li>


            </ul>
        </div>
    </div>
</nav>