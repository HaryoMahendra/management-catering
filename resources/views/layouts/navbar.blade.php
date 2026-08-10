<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow justify-content-between">

    <!-- SISI KIRI TOPBAR -->
    <div class="d-flex align-items-center">
        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Tanggal (Pojok Kiri Topbar) -->
        <div class="d-none d-md-flex align-items-center bg-light px-3 py-1 rounded border">
            <i class="fas fa-calendar-alt text-primary mr-2"></i>
            <span class="text-xs font-weight-bold text-primary text-uppercase" id="current-day-date">
                --
            </span>
        </div>
    </div>

    <!-- SISI KANAN TOPBAR -->
    <ul class="navbar-nav align-items-center">

        <!-- Jam (Pojok Kanan Topbar) -->
        <li class="nav-item d-none d-md-inline-block mr-3">
            <div class="d-flex align-items-center bg-light px-3 py-1 rounded border">
                <i class="fas fa-clock text-dark mr-2"></i>
                <span class="small font-weight-bold text-dark" id="current-time">
                    00:00:00 WIB
                </span>
            </div>
        </li>

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- NAV ITEM - USER INFORMATION & LOGOUT -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">
                    <span class="badge badge-primary ml-1 text-uppercase" style="font-size: 10px;">
                        {{ str_replace('_', ' ', Auth::user()->role ?? 'Guest') }}
                    </span>
                </span>
                <img class="img-profile rounded-circle" style="object-fit: cover;"
    src="{{ Auth::user()->foto ? asset('storage/profile/' . Auth::user()->foto) : asset('sbadmin/img/undraw_profile.svg') }}">
            </a>

            <!-- Dropdown Profile User -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <!-- Tombol Profile-->
                <!-- Tombol Profile-->
<a class="dropdown-item" href="{{ route('profile.index') }}">
    <i class="fas fa-user fa-sm fa-fw mr-2 text-primary"></i>
    Profile
</a>

                <div class="dropdown-divider"></div>

                <!-- Tombol Logout -->
                <a class="dropdown-item text-danger font-weight-bold" href="#" data-toggle="modal"
                    data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->

<!-- Script Realtime Clock -->
<script>
    function updateClock() {
        const now = new Date();

        const optionsDate = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const dateString = now.toLocaleDateString('id-ID', optionsDate);

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds} WIB`;

        if (document.getElementById('current-day-date')) {
            document.getElementById('current-day-date').innerText = dateString;
        }
        if (document.getElementById('current-time')) {
            document.getElementById('current-time').innerText = timeString;
        }
    }

    updateClock();
    setInterval(updateClock, 1000);
</script>
