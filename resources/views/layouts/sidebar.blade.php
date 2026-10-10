<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand (Logo Header Top) -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <img src="{{ asset('logo2.png') }}" alt="Logo" class="img-fluid" style="max-height: 40px; width: auto;">
        <div class="sidebar-brand-text mx-3">Management Catering</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard (SEMUA ROLE) -->
    <li class="nav-item {{ request()->is('/') || request()->is('dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- ========================================================================= -->
    <!-- MENU KHUSUS MASTER ADMIN (DROPDOWN)                                       -->
    <!-- ========================================================================= -->
    @if (Auth::check() && Auth::user()->email === 'mastergcpi26@godrej.com')
        <hr class="sidebar-divider">

        <li class="nav-item {{ request()->routeIs('user.master.index', 'user.activity.index') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster"
                aria-expanded="{{ request()->routeIs('user.master.index', 'user.activity.index') ? 'true' : 'false' }}" aria-controls="collapseMaster">
                <i class="fas fa-users-cog"></i>
                <span>Menu Master</span>
            </a>
            <div id="collapseMaster" class="collapse {{ request()->routeIs('user.master.index', 'user.activity.index') ? 'show' : '' }}" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('user.master.index') ? 'active' : '' }}" href="{{ route('user.master.index') }}">Data User Master</a>
                    <a class="collapse-item {{ request()->routeIs('user.activity.index') ? 'active' : '' }}" href="{{ route('user.activity.index') }}">Activity User</a>
                </div>
            </div>
        </li>
    @endif

    <!-- ========================================================================= -->
    <!-- MENU KHUSUS SUPER ADMIN ONLY (DROPDOWN)                                   -->
    <!-- ========================================================================= -->
    @if (Auth::check() && Auth::user()->role === 'super_admin')
        <hr class="sidebar-divider">

        <li class="nav-item {{ request()->is('user*', 'role*') && !request()->is('user-master*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUser"
                aria-expanded="{{ request()->is('user*', 'role*') && !request()->is('user-master*') ? 'true' : 'false' }}" aria-controls="collapseUser">
                <i class="fa-solid fa-users-gear"></i>
                <span>Menu User</span>
            </a>
            <div id="collapseUser" class="collapse {{ request()->is('user*', 'role*') && !request()->is('user-master*') ? 'show' : '' }}" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->is('user') || (request()->is('user/*') && !request()->is('user-master*')) ? 'active' : '' }}" href="{{ route('user.index') }}">Data User</a>
                    <a class="collapse-item {{ request()->is('role*') ? 'active' : '' }}" href="{{ route('role.index') }}">Role User</a>
                </div>
            </div>
        </li>
    @endif

    <!-- ========================================================================= -->
    <!-- MENU SCAN: BISA DIAKSES SEMUA ROLE (DROPDOWN)                             -->
    <!-- ========================================================================= -->
    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->is('scan*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseScan"
            aria-expanded="{{ request()->is('scan*') ? 'true' : 'false' }}" aria-controls="collapseScan">
            <i class="fas fa-fw fa-qrcode"></i>
            <span>Menu Scan</span>
        </a>
        <div id="collapseScan" class="collapse {{ request()->is('scan*') ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('scan*') ? 'active' : '' }}" href="{{ route('scan.index') }}">Scan QR Catering</a>
            </div>
        </div>
    </li>

    <!-- ========================================================================= -->
    <!-- MENU KHUSUS SUPER ADMIN & ADMIN CATERING ONLY (DROPDOWN)                  -->
    <!-- ========================================================================= -->
    @if (Auth::check() && in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
        <hr class="sidebar-divider">

        <li class="nav-item {{ request()->is('booking*', 'barcode*', 'cardcatering*', 'whatsapp*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataKaryawan"
                aria-expanded="{{ request()->is('booking*', 'barcode*', 'cardcatering*', 'whatsapp*') ? 'true' : 'false' }}" aria-controls="collapseDataKaryawan">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Menu Data Karyawan</span>
            </a>
            <div id="collapseDataKaryawan" class="collapse {{ request()->is('booking*', 'barcode*', 'cardcatering*', 'whatsapp*') ? 'show' : '' }}" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->is('booking*') ? 'active' : '' }}" href="{{ route('booking.index') }}">Booking Catering</a>
                    <a class="collapse-item {{ request()->is('barcode*') || request()->is('cardcatering*') ? 'active' : '' }}" href="{{ route('kartu.index') }}">Kartu Karyawan</a>
                    <a class="collapse-item {{ request()->is('whatsapp*') ? 'active' : '' }}" href="{{ route('whatsapp.index') }}">WhatsApp Catering</a>
                </div>
            </div>
        </li>
    @endif

    <!-- ========================================================================= -->
    <!-- MENU LAPORAN: SUPER ADMIN, ADMIN CATERING & PETUGAS CATERING (DROPDOWN)   -->
    <!-- ========================================================================= -->
    @if (Auth::check() && in_array(Auth::user()->role, ['super_admin', 'admin_catering', 'petugas_catering']))
        <hr class="sidebar-divider">

        <li class="nav-item {{ request()->is('laporan*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan"
                aria-expanded="{{ request()->is('laporan*') ? 'true' : 'false' }}" aria-controls="collapseLaporan">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>Menu Laporan</span>
            </a>
            <div id="collapseLaporan" class="collapse {{ request()->is('laporan*') ? 'show' : '' }}" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">Laporan</a>
                </div>
            </div>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    {{-- <!-- Sidebar Toggler (Tombol Bulat Panah Samping) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div> --}}

</ul>
<!-- End of Sidebar -->