@extends('master')

@section('title', 'Booking Catering')

@section('content')
    <!-- Header Judul Halaman -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Booking Catering</h1>
    </div>

    <!-- 1. CARD FILTER DATA BOOKING -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
             <h6 class="m-0 font-weight-bold text-primary">
                 <i class="fas fa-solid fa-filter"></i>Filter Data Booking Catering
            </h6>

            <!-- Tombol Upload Excel -->
            @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
                <button type="button" class="btn btn-success btn-sm shadow-sm font-weight-bold" data-toggle="modal"
                    data-target="#uploadExcelModal">
                    <i class="fas fa-file-excel fa-sm text-white-50 mr-2"></i> Upload Data Excel
                </button>
            @endif
        </div>

        <div class="card-body">
            <div class="row align-items-end">
                <!-- Filter Tanggal -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Filter Tanggal</label>
                    <input type="date" id="filterTanggal" class="form-control font-weight-bold">
                </div>

                <!-- Filter Shift -->
                <div class="col-lg-2 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Shift</label>
                    <select id="filterShift" class="form-control font-weight-bold">
                        <option value="">-- Semua --</option>
                        <option value="Shift 1">Shift 1</option>
                        <option value="Shift 2">Shift 2</option>
                    </select>
                </div>

                <!-- Filter Lokasi -->
                <div class="col-lg-2 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Lokasi</label>
                    <select id="filterLokasi" class="form-control font-weight-bold">
                        <option value="">-- Semua --</option>
                        <option value="Kantin 1">Kantin 1</option>
                        <option value="Kantin 2">Kantin 2</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-lg-2 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Status Pengambilan</label>
                    <select id="filterStatus" class="form-control font-weight-bold">
                        <option value="">-- Semua --</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>

                <!-- Tombol Tampilkan -->
                <div class="col-lg-3 col-md-12 mb-3 d-flex">
                    <button id="btnTampilkan" class="btn btn-primary font-weight-bold flex-grow-1 mr-2 shadow-sm">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. CARD TABEL DATA BOOKING CATERING -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-light border-bottom">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Daftar Data Booking Catering
            </h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark font-weight-bold">
                        <tr>
                            <th width="5%">No</th>
                            <th>NIF</th>
                            <th>Nama Karyawan</th>
                            <th>Departemen</th>
                            <th>Lokasi</th>
                            <th>Shift</th>
                            <th>No. WhatsApp</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawans as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold text-dark">{{ $item->nif }}</td>
                                <td class="text-uppercase">{{ $item->nama }}</td>
                                <td>{{ $item->departemen ?? '-' }}</td>
                                <td>{{ $item->lokasi ?? '-' }}</td>
                                <td>{{ $item->shift ?? '-' }}</td>
                                <td>
                                    @if ($item->no_hp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_hp) }}"
                                            target="_blank" class="text-success text-decoration-none font-weight-bold">
                                            {{ $item->no_hp }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($item->created_at)
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status_catering == 'Sudah')
                                        <span class="badge bg-success text-white px-3 py-2 rounded-pill">Sudah</span>
                                    @else
                                        <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <!-- Tombol Edit -->
                                    @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
                                        <button class="btn btn-warning btn-sm rounded shadow-sm text-white mr-1"
                                            data-toggle="modal" data-target="#editModal{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif

                                    <!-- Tombol Hapus -->
                                    @if (Auth::user()->role == 'super_admin')
                                        <form action="{{ route('booking.delete', $item->id) }}" method="POST"
                                            class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-danger btn-sm rounded shadow-sm btn-delete"
                                                data-name="{{ $item->nama }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Belum ada data booking catering terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT DATA KARYAWAN -->
    @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
        @foreach($karyawans as $item)
            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Data Karyawan</h5>
                            <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form id="formEdit{{ $item->id }}" action="{{ route('booking.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body text-left">
                                <div class="row">
                                    <!-- Kolom Kiri -->
                                    <div class="col-md-6 border-right">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">NIF</label>
                                            <input type="text" name="nif" class="form-control" value="{{ $item->nif }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Nama Karyawan</label>
                                            <input type="text" name="nama" class="form-control text-uppercase" value="{{ $item->nama }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Departemen</label>
                                            <input type="text" name="departemen" class="form-control" value="{{ $item->departemen }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Lokasi</label>
                                            <input type="text" name="lokasi" class="form-control" value="{{ $item->lokasi }}">
                                        </div>
                                    </div>

                                    <!-- Kolom Kanan -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Shift</label>
                                            <input type="text" name="shift" class="form-control" value="{{ $item->shift }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">No. WhatsApp</label>
                                            <input type="text" name="no_hp" class="form-control" value="{{ $item->no_hp }}">
                                        </div>

                                        <!-- INPUT TANGGAL CATERING (NAME="TANGGAL" UTAMA) -->
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Tanggal Booking Catering</label>
                                            <input type="date" name="tanggal" class="form-control font-weight-bold" 
                                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Status Catering</label>
                                            <select name="status_catering" class="form-control font-weight-bold">
                                                <option value="belum" {{ $item->status_catering == 'Belum' ? 'selected' : '' }}>Belum Mengambil</option>
                                                <option value="sudah" {{ $item->status_catering == 'Sudah' ? 'selected' : '' }}>Sudah Mengambil</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button class="btn btn-secondary font-weight-bold" type="button" data-dismiss="modal">Batal</button>
                                <button class="btn btn-warning text-white font-weight-bold" type="submit">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Modal Upload Data Excel -->
    @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
        <div class="modal fade" id="uploadExcelModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-file-excel mr-2"></i>Upload File Excel Karyawan</h5>
                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('booking.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Pilih File Excel (.xlsx / .xls / .csv)</label>
                                <input type="file" name="file_excel" class="form-control-file border p-2 rounded w-100" required>
                            </div>
                            <small class="text-muted">
                                * Header kolom di file Excel harus berisi: <code>nif</code>, <code>nama</code>,
                                <code>departemen</code>, <code>lokasi</code>, <code>shift</code>, <code>no_hp</code>.
                            </small>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-success" type="submit">Import Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
                    "search": "Cari NIF/ Karyawan / Departemen:",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Selanjutnya"
                    }
                },
                "pageLength": 10,
                "order": [
                    [0, "asc"]
                ]
            });

            // 2. Logic Tombol Tampilkan Filter
            $('#btnTampilkan').on('click', function() {
                var shiftVal = $.fn.dataTable.util.escapeRegex($('#filterShift').val());
                table.column(5).search(shiftVal ? '^' + shiftVal + '$' : '', true, false);

                var lokasiVal = $.fn.dataTable.util.escapeRegex($('#filterLokasi').val());
                table.column(4).search(lokasiVal ? '^' + lokasiVal + '$' : '', true, false);

                var statusVal = $.fn.dataTable.util.escapeRegex($('#filterStatus').val());
                table.column(8).search(statusVal ? '^' + statusVal + '$' : '', true, false);

                var tanggalRaw = $('#filterTanggal').val();
                if (tanggalRaw) {
                    var parts = tanggalRaw.split('-'); // [YYYY, MM, DD]
                    var formattedTanggal = parts[2] + '/' + parts[1] + '/' + parts[0];
                    table.column(7).search(formattedTanggal);
                } else {
                    table.column(7).search('');
                }

                table.draw();
            });

            // Mixin Style SweetAlert2
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success mx-2",
                    cancelButton: "btn btn-danger mx-2"
                },
                buttonsStyling: false
            });

            // SweetAlert2 Hapus Data
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                let name = $(this).data('name');

                swalWithBootstrapButtons.fire({
                    title: "Apakah Anda Yakin?",
                    text: "Data karyawan " + name + " akan dihapus permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        swalWithBootstrapButtons.fire({
                            title: "Dibatalkan",
                            text: "Data karyawan aman",
                            icon: "error"
                        });
                    }
                });
            });

            // SweetAlert2 Edit Data
            $(document).on('submit', 'form[id^="formEdit"]', function(e) {
                e.preventDefault();
                let form = this;

                swalWithBootstrapButtons.fire({
                    title: "Simpan?",
                    text: "Pastikan data karyawan yang diubah sudah benar!",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Simpan!",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Pop-up Toast Otomatis
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2500
                });
            @endif
        });
    </script>
@endpush