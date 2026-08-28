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
    <!-- MENU KHUSUS SUPER ADMIN ONLY                                             -->
    <!-- ========================================================================= -->
    @if (Auth::check() && Auth::user()->role === 'super_admin')
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Menu User
        </div>

        <!-- Data User -->
        <li class="nav-item {{ request()->is('user*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.index') }}">
                <i class="fa-solid fa-users-gear"></i>
                <span>Data User</span>
            </a>
        </li>

        <!-- Role User -->
        <li class="nav-item {{ request()->is('role*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('role.index') }}">
                <i class="fas fa-solid fa-user-shield"></i>
                <span>Role User</span>
            </a>
        </li>
    @endif

    <!-- ========================================================================= -->
    <!-- MENU SCAN: BISA DIAKSES SEMUA ROLE (TERMASUK PETUGAS CATERING)             -->
    <!-- ========================================================================= -->
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Menu Scan
    </div>

    <li class="nav-item {{ request()->is('scan*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('scan.index') }}">
            <i class="fas fa-fw fa-qrcode"></i>
            <span>Scan QR Catering</span>
        </a>
    </li>

    <!-- ========================================================================= -->
    <!-- MENU KHUSUS SUPER ADMIN & ADMIN CATERING ONLY                             -->
    <!-- ========================================================================= -->
    @if (Auth::check() && in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Menu Data Karyawan
        </div>

        <!-- Nav Item - Booking Catering -->
        <li class="nav-item {{ request()->is('booking*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('booking.index') }}">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Booking Catering</span>
            </a>
        </li>

        <!-- Nav Item - Kartu Karyawan -->
        <li class="nav-item {{ request()->is('barcode*') || request()->is('cardcatering*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kartu.index') }}">
                <i class="fas fa-solid fa-address-card"></i>
                <span>Kartu Karyawan</span>
            </a>
        </li>

        {{-- <!-- Nav Item - WhatsApp Blast QR -->
        <li class="nav-item {{ request()->is('whatsapp*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('whatsapp.index') }}">
                <i class="fab fa-fw fa-whatsapp"></i>
                <span>WhatsApp Catering</span>
            </a>
        </li> --}}

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Menu Laporan
        </div>

        <!-- Nav Item - Laporan -->
        <li class="nav-item {{ request()->is('laporan*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('laporan.index') }}">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>Laporan</span>
            </a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Tombol Bulat Panah Samping) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
