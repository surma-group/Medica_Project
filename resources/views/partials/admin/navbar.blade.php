<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar sticky">
    <div class="form-inline me-auto">
        <ul class="navbar-nav mr-3">
            <li>
                <a href="#" data-bs-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                    <i data-feather="align-justify"></i>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link nav-link-lg fullscreen-btn">
                    <i data-feather="maximize"></i>
                </a>
            </li>
            <li>
                <form class="form-inline me-auto">
                    <div class="search-element d-flex">
                        <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </li>
        </ul>
    </div>

    <ul class="navbar-nav navbar-right">
        <li class="dropdown dropdown-list-toggle">
            <a href="#" data-bs-toggle="dropdown" class="nav-link nav-link-lg">
                <i data-feather="credit-card"></i>
                <span class="badge headerBadge1 bg-success">
                    ৳ {{ number_format($systemBalance, 2) }}
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right pullDown">
                <div class="dropdown-header">
                    Wallet
                </div>

                <div class="dropdown-list-content dropdown-list-icons">
                    <a href="javascript:void(0)" class="dropdown-item">
                        <span class="dropdown-item-icon bg-success text-white">
                            <i class="fas fa-wallet"></i>
                        </span>
                        &nbsp;
                        <span class="dropdown-item-desc">
                            Balance
                            <span class="time">
                                 ৳ {{ number_format($systemBalance, 2) }}
                            </span>
                        </span>
                    </a>

                    <a href="{{ route('add_money.index') }}" class="dropdown-item">
                        <span class="dropdown-item-icon bg-primary text-white">
                            <i class="fas fa-plus"></i>
                        </span>
                        &nbsp;
                        <span class="dropdown-item-desc">
                            Add Money
                        </span>
                    </a>
                </div>
            </div>
        </li>


        <li class="dropdown dropdown-list-toggle">
            <a href="#" data-bs-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
                <i data-feather="bell" class="bell"></i>
            </a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                <div class="dropdown-header">
                    Notifications
                    <div class="float-right">
                        <a href="#">Mark All As Read</a>
                    </div>
                </div>
                <div class="dropdown-list-content dropdown-list-icons">
                    <a href="#" class="dropdown-item dropdown-item-unread">
                        <span class="dropdown-item-icon bg-primary text-white">
                            <i class="fas fa-code"></i>
                        </span>
                        <span class="dropdown-item-desc">
                            Template update is available now! <span class="time">2 Min Ago</span>
                        </span>
                    </a>

                </div>
                <div class="dropdown-footer text-center">
                    <a href="#">View All <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </li>

        <li class="dropdown">
            <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ asset('admin/assets/img/user.png') }}" class="user-img-radious-style">
                <span class="d-sm-none d-lg-inline-block"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
                <div class="dropdown-title">
                    Hello {{ auth()->user()->name ?? 'Admin' }}
                </div>
                <a href="profile.html" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>
                <a href="timeline.html" class="dropdown-item has-icon">
                    <i class="fas fa-bolt"></i> Activities
                </a>
                <a href="" class="dropdown-item has-icon">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.logout') }}" class="dropdown-item has-icon text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>