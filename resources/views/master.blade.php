<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Dashboard') - Management Catering</title>

    <!-- CDN FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom fonts for this template-->
    <link href="{{ asset('sbadmin') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin') }}/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Ketika sidebar dalam kondisi minimised / toggled */
        .sidebar.toggled .sidebar-brand .sidebar-brand-text,
        .sidebar.toggled .sidebar-heading,
        .sidebar.toggled .nav-item .nav-link span {
            display: none !important;
        }

        /* Memastikan ikon berada persis di tengah saat sidebar mengecil */
        .sidebar.toggled .nav-item .nav-link {
            text-align: center;
            padding: 0.75rem 1rem;
            width: 100%;
        }

        .sidebar.toggled .nav-item .nav-link i {
            margin-right: 0 !important;
            font-size: 1.1rem;
        }

        /* Menjaga ukuran logo ikon di paling atas tetap simetris */
        .sidebar.toggled .sidebar-brand .sidebar-brand-icon i {
            font-size: 1.5rem;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Navbar -->
                @include('layouts.navbar')

                <!-- Begin Page Content (Isi Konten Dinamis Halaman) -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts.footer')


        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal Pop-up -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt mr-2"></i>Konfirmasi Logout
                    </h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    Apakah Anda yakin ingin keluar dari aplikasi <strong>Management Catering</strong>?
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary font-weight-bold" type="button"
                        data-dismiss="modal">Batal</button>

                    <!-- Form Logout Laravel (POST Method & CSRF Protected) -->
                    <form action="{{ route('logout') }}" method="POST" class="d-inline" id="logoutForm">
                        @csrf
                        <button type="submit" class="btn btn-danger font-weight-bold">
                            <i class="fas fa-power-off mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sbadmin') }}/vendor/jquery/jquery.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('sbadmin') }}/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sbadmin') }}/js/sb-admin-2.min.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

</body>

</html>
