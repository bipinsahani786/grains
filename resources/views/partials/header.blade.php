<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-head-mobile-toggler !-->
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->
        </div>
        <!--! [End] Header Left !-->

        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <!--! [Start] nxl-h-dark-light !-->
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>
                <!--! [End] nxl-h-dark-light !-->

                <!-- Notification bell removed as per request -->

                <!--! [Start] Profile !-->
                <div class="dropdown nxl-h-item ms-3">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user-image" class="user-avtar me-0 rounded-circle" style="object-fit: cover; width: 45px; height: 45px;" />
                        @else
                            <img src="{{ asset('Duralux-admin-1.0.0/assets/images/avatar/1.png') }}" alt="user-image" class="user-avtar me-0 rounded-circle" style="object-fit: cover; width: 45px; height: 45px;" />
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user-image" class="user-avtar rounded-circle flex-shrink-0" style="object-fit: cover; width: 45px; height: 45px;" />
                                @else
                                    <img src="{{ asset('Duralux-admin-1.0.0/assets/images/avatar/1.png') }}" alt="user-image" class="user-avtar rounded-circle flex-shrink-0" style="object-fit: cover; width: 45px; height: 45px;" />
                                @endif
                                <div>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <h6 class="text-dark mb-0 text-truncate" style="max-width: 150px;">{{ auth()->user()->name }}</h6>
                                        @if(auth()->user()->role === 'super_admin' || auth()->user()->email === 'superadmin@grainsaas.com')
                                            <span class="badge bg-soft-danger text-danger">SUPERADMIN</span>
                                        @else
                                            <span class="badge bg-soft-success text-success">ADMIN</span>
                                        @endif
                                    </div>
                                    <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="feather-user"></i>
                            <span>Profile Details</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">
                            <i class="feather-log-out"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
                <!--! [End] Profile !-->
            </div>
        </div>
        <!--! [End] Header Right !-->
    </div>
</header>