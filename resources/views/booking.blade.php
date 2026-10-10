@extends('master')

@section('title', 'Booking Catering')

@section('content')
    <!-- Header Judul Halaman -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Booking Catering</h1>
    </div>

    <!-- 1. CARD FILTER DATA BOOKING -->
<!-- 1. CARD FILTER DATA BOOKING -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-light border-bottom d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">
                <i class="fas fa-filter mr-1"></i>Filter Data Booking Catering
            </h6>

            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                <div class="badge badge-primary px-3 py-2 shadow-sm d-flex align-items-center mb-0"
    style="font-size: 0.85rem; font-weight: 600;">
    <i class="fa-solid fa-clipboard-user mr-2"></i>
    <span>Total: <strong id="total-booking">{{ $karyawans->unique('employee_no')->count() }}</strong> Karyawan</span>
</div>
            </div>
        </div>

        <!-- Bungkus dengan Form GET ke route booking.index -->
        <form action="{{ route('booking.index') }}" method="GET">
            <div class="card-body">
                <div class="row align-items-end">
                    <!-- Filter Tanggal -->
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Filter Tanggal</label>
                        <input type="date" name="tanggal" id="filterTanggal" class="form-control font-weight-bold" 
                               value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                    </div>

                    <!-- Filter Departemen -->
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Departemen</label>
                        <select name="departemen" id="departemenFilter" class="form-control font-weight-bold">
                            <option value="">-- Semua --</option>
                            @foreach ($karyawans->pluck('departemen')->filter()->unique()->sort() as $dept)
                                <option value="{{ $dept }}" {{ request('departemen') == $dept ? 'selected' : '' }}>
                                    {{ strtoupper($dept) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Schedule -->
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Schedule</label>
                        <select name="schedule" id="filterSchedule" class="form-control font-weight-bold">
                            <option value="">-- Semua --</option>
                            <option value="Non Shift" {{ request('schedule') == 'Non Shift' ? 'selected' : '' }}>Non Shift</option>
                            <option value="Shift 1" {{ request('schedule') == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                            <option value="Shift 2" {{ request('schedule') == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
                            <option value="Shift 3" {{ request('schedule') == 'Shift 3' ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>

                    <!-- Filter Lokasi (Ditambahkan) -->
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Location</label>
                        <select name="location" id="filterLocation" class="form-control font-weight-bold">
                            <option value="">-- Semua --</option>
                            <option value="GCPI 1" {{ request('location') == 'GCPI 1' ? 'selected' : '' }}>GCPI 1</option>
                            <option value="GCPI 2" {{ request('location') == 'GCPI 2' ? 'selected' : '' }}>GCPI 2</option>
                            <option value="GCPI 3" {{ request('location') == 'GCPI 3' ? 'selected' : '' }}>GCPI 3</option>
                        </select>
                    </div>

                    <!-- Filter Status Pengambilan (Ditambahkan) -->
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Status Pengambilan</label>
                        <select name="status_catering" id="filterStatus" class="form-control font-weight-bold">
                            <option value="">-- Semua --</option>
                            <option value="sudah" {{ request('status_catering') == 'sudah' ? 'selected' : '' }}>Sudah</option>
                            <option value="belum" {{ request('status_catering') == 'belum' ? 'selected' : '' }}>Belum</option>
                        </select>
                    </div>

                    <!-- Tombol Tampilkan -->
                    <div class="col-lg-2 col-md-6 mb-3 d-flex align-items-end">
                        <button type="submit" id="btnTampilkan" class="btn btn-primary font-weight-bold w-100 shadow-sm"
                            title="Tampilkan Filter">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 2. CARD TABEL DATA BOOKING CATERING -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-light border-bottom d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center flex-wrap" style="gap: 10px;">
            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">
                <i class="fas fa-table mr-2"></i>Daftar Data Booking Catering
            </h6>

            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
                    <!-- Tombol Tambah Data Manual -->
                    <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold mb-0" data-toggle="modal"
                        data-target="#tambahKaryawanModal">
                        <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Data
                    </button>

                    <!-- Tombol Upload Excel -->
                    <button type="button" class="btn btn-success btn-sm shadow-sm font-weight-bold mb-0" data-toggle="modal"
                        data-target="#uploadExcelModal">
                        <i class="fas fa-file-excel fa-sm text-white-50 mr-2"></i> Upload Data Excel
                    </button>
                @endif

                @if (Auth::user()->role == 'super_admin')
                    <form id="bulkDeleteForm" action="{{ route('booking.delete-multiple') }}" method="POST"
                        class="d-inline-block m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                            disabled>
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark font-weight-bold">
                        <tr>
                            @if (Auth::user()->role == 'super_admin')
                                <th width="4%" class="text-center">
                                    <input type="checkbox" id="select-all-bookings" title="Pilih semua data">
                                </th>
                            @endif
                            <th width="4%">No</th>
                            <th>Employee No</th>
                            <th>Name</th>
                            <th>Employment Under</th>
                            <th>Dept</th>
                            <th>Indonesia Level</th>
                            <th>Location</th>
                            <th>Schedule</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawans as $index => $item)
                            <tr>
                                @if (Auth::user()->role == 'super_admin')
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="row-checkbox" value="{{ $item->id }}"
                                            aria-label="Pilih karyawan {{ $item->name ?? $item->nama }}">
                                    </td>
                                @endif
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold text-dark">{{ $item->employee_no ?? $item->nif }}</td>
                                <td class="text-uppercase">{{ $item->name ?? $item->nama }}</td>
                                <td>{{ $item->employment_under ?? '-' }}</td>
                                <td>{{ $item->departemen ?? '-' }}</td>
                                <td>{{ $item->indonesia_level ?? '-' }}</td>
                                <td>{{ $item->location ?? ($item->lokasi ?? '-') }}</td>
                                <td>{{ $item->normalized_schedule }}</td>
                                <td>
                                    @if ($item->created_at)
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if (strtolower($item->status_catering ?? '') == 'sudah')
                                        <span class="badge bg-success text-white px-3 py-2 rounded-pill">Sudah</span>
                                    @else
                                        <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                                        @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
                                            <button class="btn btn-warning btn-sm rounded shadow-sm text-white"
                                                data-toggle="modal" data-target="#editModal{{ $item->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif

                                        @if (Auth::user()->role == 'super_admin')
                                            <form action="{{ route('booking.delete', $item->id) }}" method="POST"
                                                class="d-inline form-delete m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-danger btn-sm rounded shadow-sm btn-delete"
                                                    data-name="{{ $item->name ?? $item->nama }}" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">Belum ada data booking catering terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH DATA KARYAWAN -->
    @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
        <div class="modal fade" id="tambahKaryawanModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-plus mr-2"></i>Tambah Data Karyawan Baru</h5>
                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <div class="modal-body text-left">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6 border-right">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Employee No</label>
                                        <input type="text" name="employee_no" class="form-control" placeholder="Masukkan nomor karyawan" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Name</label>
                                        <input type="text" name="name" class="form-control text-uppercase" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Employment Under</label>
                                        <input type="text" name="employment_under" class="form-control" placeholder="Contoh: PT / LPK">
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Dept</label>
                                        <input type="text" name="departemen" class="form-control" placeholder="Masukkan departemen">
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Indonesia Level</label>
                                        <input type="text" name="indonesia_level" class="form-control" placeholder="Contoh: Staff / Non Staff">
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Location</label>
                                        <select name="location" class="form-control font-weight-bold" required>
                                            <option value="">-- Pilih Location --</option>
                                            <option value="GCPI 1">GCPI 1</option>
                                            <option value="GCPI 2">GCPI 2</option>
                                            <option value="GCPI 3">GCPI 3</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Schedule</label>
                                        <select name="schedule" class="form-control font-weight-bold" required>
                                            <option value="">-- Pilih Schedule --</option>
                                            <option value="Non Shift">Non Shift</option>
                                            <option value="Shift 1">Shift 1</option>
                                            <option value="Shift 2">Shift 2</option>
                                            <option value="Shift 3">Shift 3</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">Status Catering</label>
                                        <select name="status_catering" class="form-control font-weight-bold" required>
                                            <option value="belum">Belum Mengambil</option>
                                            <option value="sudah">Sudah Mengambil</option>
                                        </select>
                                    </div>
                     <div class="form-group">
    <label class="font-weight-bold text-dark">Tanggal Booking</label>
    <input type="date" name="tanggal" class="form-control" 
        value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button class="btn btn-secondary font-weight-bold" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary font-weight-bold" type="submit">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL EDIT DATA KARYAWAN -->
    @if (in_array(Auth::user()->role, ['super_admin', 'admin_catering']))
        @foreach ($karyawans as $item)
            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Data Karyawan</h5>
                            <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form id="formEdit{{ $item->id }}" action="{{ route('booking.update', $item->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body text-left">
                                <div class="row">
                                    <!-- Kolom Kiri -->
                                    <div class="col-md-6 border-right">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Employee No</label>
                                            <input type="text" name="employee_no" class="form-control"
                                                value="{{ $item->employee_no ?? $item->nif }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Name</label>
                                            <input type="text" name="name" class="form-control text-uppercase"
                                                value="{{ $item->name ?? $item->nama }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Employment Under</label>
                                            <input type="text" name="employment_under" class="form-control"
                                                value="{{ $item->employment_under }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Dept</label>
                                            <input type="text" name="departemen" class="form-control"
                                                value="{{ $item->departemen }}">
                                        </div>
                                    </div>

                                    <!-- Kolom Kanan -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Indonesia Level</label>
                                            <input type="text" name="indonesia_level" class="form-control"
                                                value="{{ $item->indonesia_level }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Location</label>
                                            <select name="location" class="form-control font-weight-bold">
                                                <option value="">-- Pilih Location --</option>
                                                <option value="GCPI 1"
                                                    {{ strtolower($item->location ?? ($item->lokasi ?? '')) == 'gcpi 1' ? 'selected' : '' }}>
                                                    GCPI 1</option>
                                                <option value="GCPI 2"
                                                    {{ strtolower($item->location ?? ($item->lokasi ?? '')) == 'gcpi 2' ? 'selected' : '' }}>
                                                    GCPI 2</option>
                                                <option value="GCPI 3"
                                                    {{ strtolower($item->location ?? ($item->lokasi ?? '')) == 'gcpi 3' ? 'selected' : '' }}>
                                                    GCPI 3</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Schedule</label>
                                            <select name="schedule" class="form-control font-weight-bold">
                                                <option value="">-- Pilih Schedule --</option>
                                                <option value="Non Shift"
                                                    {{ in_array(strtolower($item->schedule ?? ($item->shift ?? '')), ['non shift', 'nonshift']) ? 'selected' : '' }}>
                                                    Non Shift</option>
                                                <option value="Shift 1"
                                                    {{ in_array(strtolower($item->schedule ?? ($item->shift ?? '')), ['shift 1', 'shift1']) ? 'selected' : '' }}>
                                                    Shift 1</option>
                                                <option value="Shift 2"
                                                    {{ in_array(strtolower($item->schedule ?? ($item->shift ?? '')), ['shift 2', 'shift2']) ? 'selected' : '' }}>
                                                    Shift 2</option>
                                                <option value="Shift 3"
                                                    {{ in_array(strtolower($item->schedule ?? ($item->shift ?? '')), ['shift 3', 'shift3']) ? 'selected' : '' }}>
                                                    Shift 3</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Status Catering</label>
                                            <select name="status_catering" class="form-control font-weight-bold">
                                                <option value="belum"
                                                    {{ strtolower($item->status_catering ?? '') == 'belum' ? 'selected' : '' }}>
                                                    Belum Mengambil</option>
                                                <option value="sudah"
                                                    {{ strtolower($item->status_catering ?? '') == 'sudah' ? 'selected' : '' }}>
                                                    Sudah Mengambil</option>
                                            </select>
                                        </div>
                                        <!-- Tambahan Input Tanggal Booking pada Edit Modal -->
                                        <div class="form-group">
    <label class="font-weight-bold text-dark">Tanggal Booking</label>
    <input type="date" name="tanggal" class="form-control" 
        value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button class="btn btn-secondary font-weight-bold" type="button"
                                    data-dismiss="modal">Batal</button>
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
                                <input type="file" name="file_excel"
                                    class="form-control-file border p-2 rounded w-100" required>
                            </div>
                            <small class="text-muted">
                                * Header kolom di file Excel harus berisi: <code>employee_no</code>, <code>name</code>,
                                <code>employment_under</code>, <code>dept</code>, <code>indonesia_level</code>,
                                <code>location</code>, <code>schedule</code>.
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
    <link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            var hasCheckbox = {{ Auth::user()->role == 'super_admin' ? 'true' : 'false' }};
            
            var table = $('#dataTable').DataTable({
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "search": "Cari Employee No / Name / Dept:",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Selanjutnya"
                    }
                },
                "columnDefs": [{
                        targets: 0,
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: hasCheckbox ? 11 : 10,
                        orderable: false,
                        searchable: false
                    }
                ],
                "pageLength": 10,
                "order": [
                    [hasCheckbox ? 2 : 1, "asc"]
                ],
                "drawCallback": function(settings) {
        var api = this.api();
        
        // Ambil seluruh data employee_no dari baris yang sedang ter-filter
        var employeeNos = api.rows({ filter: 'applied' }).nodes().to$().map(function() {
            // Ambil teks dari kolom Employee No (sesuaikan indeks kolomnya, misal kolom ke-2 atau ke-3)
            return $(this).find('td:eq(' + (hasCheckbox ? 2 : 1) + ')').text().trim();
        }).get();

        // Hitung jumlah NIK/Employee No yang unik saja (buang duplikat)
        var uniqueCount = [...new Set(employeeNos)].length;

        $('#total-booking').text(uniqueCount);
        syncBulkSelectionState();
    }
            });

            function getSelectedIds() {
                return $('.row-checkbox:checked').map(function() {
                    return this.value;
                }).get();
            }

            function syncBulkSelectionState() {
                var selectedIds = getSelectedIds();
                $('#selectedCount').text(selectedIds.length);
                $('#bulkDeleteBtn').prop('disabled', selectedIds.length === 0);

                var rowCheckboxes = $('.row-checkbox');
                if (rowCheckboxes.length > 0) {
                    $('#select-all-bookings').prop('checked', rowCheckboxes.length === rowCheckboxes.filter(
                        ':checked').length);
                } else {
                    $('#select-all-bookings').prop('checked', false);
                }
            }

            $('#select-all-bookings').on('click', function(e) {
                e.stopPropagation();
            });

            $('#select-all-bookings').on('change', function() {
                var checked = this.checked;
                var visibleRows = table.rows({
                    search: 'applied',
                    page: 'current'
                }).nodes();
                $(visibleRows).find('.row-checkbox').prop('checked', checked);
                syncBulkSelectionState();
            });

            $(document).on('change', '.row-checkbox', function() {
                syncBulkSelectionState();
            });

            $('#bulkDeleteBtn').on('click', function(e) {
                e.preventDefault();
                var selectedIds = getSelectedIds();

                if (selectedIds.length === 0) {
                    return;
                }

                swalWithBootstrapButtons.fire({
                    title: 'Hapus Data Terpilih?',
                    text: 'Anda akan menghapus ' + selectedIds.length +
                        ' data karyawan yang terpilih.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#bulkDeleteForm input[name="ids[]"]').remove();

                        $.each(selectedIds, function(index, id) {
                            $('#bulkDeleteForm').append(
                                '<input type="hidden" name="ids[]" value="' + id + '">');
                        });

                        $('#bulkDeleteForm').submit();
                    }
                });
            });

            // Pemetaan indeks kolom DataTables yang akurat (dengan atau tanpa checkbox)
            var offset = hasCheckbox ? 1 : 0;

            $('#btnTampilkan').on('click', function() {
                // Tentukan kolom index secara pasti berdasarkan elemen tabel yang ada di DOM
                // Urutan kolom: 0=No, 1=Employee No, 2=Name, 3=Employment Under, 4=Dept, 5=Indonesia Level, 6=Location, 7=Schedule, 8=Tanggal, 9=Status, 10=Aksi
                // Jika kolom checkbox ada di indeks 0 (super_admin), maka semua indeks bergeser +1
                var colOffset = $('#dataTable th:contains("Employee No")').index() - 1;

                // Filter Departemen
                var deptVal = $.fn.dataTable.util.escapeRegex($('#departemenFilter').val());
                table.column(4 + colOffset).search(deptVal ? '^' + deptVal + '$' : '', true, false);

                // Filter Location
                var locationVal = $.fn.dataTable.util.escapeRegex($('#filterLocation').val());
                table.column(6 + colOffset).search(locationVal ? '^' + locationVal + '$' : '', true, false);

                // Filter Schedule
                var scheduleVal = $.fn.dataTable.util.escapeRegex($('#filterSchedule').val());
                table.column(7 + colOffset).search(scheduleVal ? '^' + scheduleVal + '$' : '', true, false);

                // Filter Tanggal
                var tanggalRaw = $('#filterTanggal').val();
                if (tanggalRaw) {
                    var parts = tanggalRaw.split('-');
                    var formattedTanggal = parts[2] + '/' + parts[1] + '/' + parts[0];
                    table.column(8 + colOffset).search(formattedTanggal);
                } else {
                    table.column(8 + colOffset).search('');
                }

                // Filter Status Pengambilan
                var statusVal = $.fn.dataTable.util.escapeRegex($('#filterStatus').val());
                table.column(9 + colOffset).search(statusVal ? '^' + statusVal + '$' : '', true, false);

                table.draw();
            });

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success mx-2",
                    cancelButton: "btn btn-danger mx-2"
                },
                buttonsStyling: false
            });

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