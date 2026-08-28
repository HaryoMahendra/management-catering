@extends('master')

@section('title', 'Laporan Catering')

@section('content')

<!-- Header Khusus Tampilan Cetak (Hanya Muncul Saat Print / Save PDF) -->
<div class="print-header">
    <h3 class="text-center font-weight-bold text-dark mb-1">LAPORAN PENGAMBILAN CATERING KARYAWAN</h3>
    <p class="text-center text-dark mb-2">
        Tanggal: <strong>{{ date('d/m/Y', strtotime($startDate)) }}</strong> - <strong>{{ date('d/m/Y', strtotime($endDate)) }}</strong>
    </p>
    <hr class="border-dark my-3">
</div>

<!-- Header Page Biasa (Tampil di Browser) -->
<div class="mb-4 no-print">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Laporan</h1>
</div>

<!-- Card Filter Laporan -->
<div class="card shadow mb-4 border-0 no-print">
    <!-- Header Card: Judul & Tombol Cetak Laporan Sejajar -->
    <div class="card-header py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
                 <i class="fas fa-solid fa-filter"></i>Filter Data Laporan
            </h6>
        <button type="button" id="btnCetak" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
            Cetak Laporan
        </button>
    </div>

    <div class="card-body">
        <form action="{{ route('laporan.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold text-dark">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold text-dark">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold text-dark">Status Pengambilan</label>
                    <select name="status" class="form-control">
                        <option value="semua" {{ $filterStatus == 'semua' ? 'selected' : '' }}>-- Semua Status --</option>
                        <option value="sudah" {{ $filterStatus == 'sudah' ? 'selected' : '' }}>Sudah Mengambil</option>
                        <option value="belum" {{ $filterStatus == 'belum' ? 'selected' : '' }}>Belum Mengambil</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Detail Laporan -->
<div class="card shadow mb-4 border-0 card-print-clean">
    <div class="card-header py-3 bg-white border-bottom-0 no-print">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>Data Pengambilan (Tanggal: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }})
        </h6>
    </div>
    <div class="card-body p-0-print">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-print-bordered" id="dataTableLaporan" width="100%" cellspacing="0">
                <thead class="bg-light text-dark font-weight-bold">
                    <tr>
                        <th width="5%">No</th>
                        <th>NIF</th>
                        <th>Nama Karyawan</th>
                        <th>Departemen</th>
                        <th>Lokasi</th>
                        <th>Shift</th>
                        <th>Waktu Scan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $index => $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-weight-bold text-dark">{{ $item->nif }}</td>
                        <td class="text-uppercase">{{ $item->nama }}</td>
                        <td>{{ $item->departemen ?? '-' }}</td>
                        <td>{{ $item->lokasi ?? '-' }}</td>
                        <td>{{ $item->shift ?? '-' }}</td>
                        <td>
                            @if($item->waktu_scan != '-')
                                {{-- <span class="badge badge-light border text-dark p-2 print-no-badge"> --}}
                                    {{-- <i class="fas fa-clock mr-1 text-primary no-print"></i> --}}
                                    {{ $item->waktu_scan }} WIB
                                {{-- </span> --}}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->status_catering == 'Sudah')
                                <span class="badge bg-success text-white px-3 py-2 rounded-pill print-no-badge">Sudah</span>
                            @else
                                <span class="badge bg-danger text-white px-3 py-2 rounded-pill print-no-badge">Belum</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada data untuk periode tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables Plugin -->
<link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<style>
    /* 1. Sembunyikan Header Cetak di Layar Biasa Browser */
    .print-header {
        display: none;
    }

    /* 2. ATURAN KHUSUS CETAK (PRINT / PDF) */
    @media print {
        /* Sembunyikan elemen UI bawaan Admin */
        .sidebar, 
        .topbar, 
        .no-print, 
        footer,
        .dataTables_length, 
        .dataTables_filter, 
        .dataTables_paginate, 
        .dataTables_info {
            display: none !important;
        }

        /* Hilangkan background gambar dan pseudo panah bawaan DataTables */
        table.dataTable thead th,
        table.dataTable thead td,
        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            background-image: none !important;
            padding-right: 8px !important;
        }

        table.dataTable thead .sorting::before,
        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::before,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::before,
        table.dataTable thead .sorting_desc::after,
        table.dataTable thead th::before,
        table.dataTable thead th::after {
            display: none !important;
            content: "" !important;
        }

        /* Tampilkan Header Kop Surat Laporan */
        .print-header {
            display: block !important;
        }

        /* Reset Layout agar memenuhi kertas secara rapi */
        body {
            background-color: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            color: #000000 !important;
        }

        #content-wrapper, #content, .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            background-color: #ffffff !important;
        }

        .card-print-clean {
            border: none !important;
            box-shadow: none !important;
        }

        .p-0-print {
            padding: 0 !important;
        }

        /* Format Garis Tabel Hitam Tegas untuk Hasil Cetak Kertas */
        .table-print-bordered {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .table-print-bordered th, 
        .table-print-bordered td {
            border: 1px solid #000000 !important;
            padding: 6px 8px !important;
            color: #000000 !important;
            font-size: 12px !important;
        }

        .table-print-bordered thead th {
            background-color: #f0f0f0 !important;
            text-align: center;
        }

        /* Hilangkan gaya badge warna tebal agar cetakan kertas hemat tinta dan bersih */
        .print-no-badge {
            border: none !important;
            background: none !important;
            color: #000000 !important;
            font-weight: bold !important;
            padding: 0 !important;
        }
    }
</style>

<script>
    $(document).ready(function() {
        var table = $('#dataTableLaporan').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
                "infoEmpty": "Tidak ada data tersedia",
                "search": "Cari Karyawan / NIK / Departemen:",
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya"
                }
            },
            "pageLength": 10,
            "order": [[ 0, "asc" ]]
        });

        $('#btnCetak').on('click', function() {
            var currentLength = table.page.len();
            
            table.page.len(-1).draw();

            setTimeout(function() {
                window.print();
                
                table.page.len(currentLength).draw();
            }, 300);
        });
    });
</script>
@endpush