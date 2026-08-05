@extends('master')

@section('title', 'Role')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Role User</h1>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-light border-bottom">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class=" fas fa-solid fa-key"></i> Hak Akses Role User
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark font-weight-bold">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Hak Akses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $r['nama'] }}</strong></td>
                            <td><code>{{ $r['slug'] }}</code></td>
                            <td>{{ $r['deskripsi'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <!-- DataTables JavaScript & CSS Plugin -->
    <link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Inisialisasi DataTables
            var table = $('#dataTable').DataTable({
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
            
                },
                "pageLength": 5,
                "order": [
                    [0, "asc"]
                ]
            });
        });
    </script>
@endpush