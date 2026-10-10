@extends('master')

@section('title', 'Data User Master')

@section('content')
    <div class="container-fluid">

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Data User Master</h1>
            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahUserMaster">
                <i class="fas fa-plus mr-1"></i> Tambah User Master
            </button>
        </div>

        <!-- ================= TABEL DATA USER MASTER ================= -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fa-solid fa-house-chimney-user"></i> Daftar Akun Pengelola Sistem
                </h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark font-weight-bold">
                            <tr>
                                <th width="5%">No</th>
                                <th width="18%">Nama</th>
                                <th width="18%">Email</th>
                                <th width="12%">Role</th>
                                <th width="12%">Akses Lokasi</th>
                                <th width="25%">Akses Departemen</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $usr)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $usr->name }}</td>
                                    <td>{{ $usr->email }}</td>
                                    <td>
                                        @if ($usr->role == 'super_admin')
                                            <span class="badge badge-danger">Super Admin</span>
                                        @elseif($usr->role == 'admin_catering')
                                            <span class="badge badge-warning text-dark">Admin Catering</span>
                                        @else
                                            <span class="badge badge-info">Petugas Catering</span>
                                        @endif
                                    </td>

                                    <!-- Akses Lokasi -->
                                    <td>
                                        @php
                                            $locs = is_array($usr->lokasi)
                                                ? $usr->lokasi
                                                : explode(',', $usr->lokasi ?? '');
                                            $locs = array_filter(array_map('trim', $locs));
                                        @endphp
                                        @if (!empty($locs))
                                            @foreach ($locs as $lok)
                                                <span class="badge badge-info mb-1">{{ $lok }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge badge-secondary">Semua Lokasi</span>
                                        @endif
                                    </td>

                                    <!-- Akses Departemen -->
                                    <td>
                                        @php
                                            $depts = is_array($usr->departemen)
                                                ? $usr->departemen
                                                : explode(',', $usr->departemen ?? '');
                                            $depts = array_filter(array_map('trim', $depts));
                                        @endphp
                                        @if (!empty($depts))
                                            <div class="dept-badge-container">
                                                @foreach ($depts as $dept)
                                                    <span class="badge badge-dark mb-1">{{ $dept }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="badge badge-secondary">Semua Departemen</span>
                                        @endif
                                    </td>

                                    <!-- Tombol Aksi -->
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                                            <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                                data-target="#modalEditUserMaster{{ $usr->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form action="{{ route('user.master.destroy', $usr->id) }}" method="POST"
                                                class="d-inline form-delete-{{ $usr->id }} m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger shadow-sm"
                                                    onclick="confirmDelete('{{ $usr->id }}', '{{ $usr->name }}')"
                                                    title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- ===================== MODAL EDIT USER MASTER ===================== -->
                                <div class="modal fade" id="modalEditUserMaster{{ $usr->id }}" tabindex="-1"
                                    role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form action="{{ route('user.master.update', $usr->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title font-weight-bold">Edit Data User Master</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <div class="row">
                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold">Nama Lengkap</label>
                                                            <input type="text" name="name" class="form-control"
                                                                value="{{ $usr->name }}" required>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold">Email</label>
                                                            <input type="email" name="email" class="form-control"
                                                                value="{{ $usr->email }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Role / Hak Akses</label>
                                                        <div class="single-select-field" data-single-select
                                                            data-placeholder="-- Pilih Role --">
                                                            <div class="single-select-box" tabindex="0">
                                                                <div class="single-select-selected"></div>
                                                                <span class="single-select-caret"></span>
                                                            </div>
                                                            <div class="single-select-dropdown" style="display:none;">
                                                                @foreach ([['value' => 'super_admin', 'label' => 'Super Admin'], ['value' => 'admin_catering', 'label' => 'Admin Catering'], ['value' => 'petugas_catering', 'label' => 'Petugas Catering']] as $roleOption)
                                                                    <div class="single-option {{ $usr->role == $roleOption['value'] ? 'selected' : '' }}"
                                                                        data-value="{{ $roleOption['value'] }}">
                                                                        <span>{{ $roleOption['label'] }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <select name="role" class="d-none" required>
                                                                <option value="">-- Pilih Role --</option>
                                                                <option value="super_admin"
                                                                    {{ $usr->role == 'super_admin' ? 'selected' : '' }}>
                                                                    Super Admin</option>
                                                                <option value="admin_catering"
                                                                    {{ $usr->role == 'admin_catering' ? 'selected' : '' }}>
                                                                    Admin Catering</option>
                                                                <option value="petugas_catering"
                                                                    {{ $usr->role == 'petugas_catering' ? 'selected' : '' }}>
                                                                    Petugas Catering</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Akses Lokasi Tugas</label>
                                                        <div class="multi-select-field" data-multi-select
                                                            data-placeholder="-- Pilih Akses Lokasi --">
                                                            <div class="multi-select-box" tabindex="0">
                                                                <div class="multi-select-selected"></div>
                                                                <span class="multi-select-caret"></span>
                                                            </div>
                                                            <div class="multi-select-dropdown" style="display:none;">
                                                                @foreach (['GCPI 1', 'GCPI 2', 'GCPI 3'] as $lok)
                                                                    <label class="multi-option">
                                                                        <input type="checkbox"
                                                                            value="{{ $lok }}"
                                                                            {{ is_array($usr->lokasi) ? (in_array($lok, $usr->lokasi, true) ? 'checked' : '') : ($usr->lokasi == $lok ? 'checked' : '') }}>
                                                                        <span>{{ $lok }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                            <select name="lokasi[]" class="d-none" multiple>
                                                                @foreach (['GCPI 1', 'GCPI 2', 'GCPI 3'] as $lok)
                                                                    <option value="{{ $lok }}"
                                                                        {{ is_array($usr->lokasi) ? (in_array($lok, $usr->lokasi, true) ? 'selected' : '') : ($usr->lokasi == $lok ? 'selected' : '') }}>
                                                                        {{ $lok }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Akses Departemen</label>
                                                        <div class="multi-select-field" data-multi-select
                                                            data-placeholder="-- Pilih Akses Departemen --">
                                                            <div class="multi-select-box" tabindex="0">
                                                                <div class="multi-select-selected"></div>
                                                                <span class="multi-select-caret"></span>
                                                            </div>
                                                            <div class="multi-select-dropdown" style="display:none;">
                                                                @foreach (['Customer Service', 'Field_Sales', 'General Affairs', 'HR & GA', 'HR Operations', 'HRPP', 'HSE', 'Indirect Sourcing', 'Industrial Relations', 'Internal Audit', 'IT', 'IT Business Partner', 'IT Infrastructure', 'Legal', 'Operating Company', 'PPIC', 'Product Quality', 'Production', 'Quasi Holding', 'Rewards', 'Secretary', 'Shared', 'Service Center', 'Technic', 'Warehouse Operation'] as $dept)
                                                                    <label class="multi-option">
                                                                        <input type="checkbox"
                                                                            value="{{ $dept }}"
                                                                            {{ is_array($usr->departemen) ? (in_array($dept, $usr->departemen, true) ? 'checked' : '') : ($usr->departemen == $dept ? 'checked' : '') }}>
                                                                        <span>{{ $dept }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                            <select name="departemen[]" class="d-none" multiple>
                                                                @foreach (['Customer Service', 'Field_Sales', 'General Affairs', 'HR & GA', 'HR Operations', 'HRPP', 'HSE', 'Indirect Sourcing', 'Industrial Relations', 'Internal Audit', 'IT', 'IT Business Partner', 'IT Infrastructure', 'Legal', 'Operating Company', 'PPIC', 'Product Quality', 'Production', 'Quasi Holding', 'Rewards', 'Secretary', 'Shared', 'Service Center', 'Technic', 'Warehouse Operation'] as $dept)
                                                                    <option value="{{ $dept }}"
                                                                        {{ is_array($usr->departemen) ? (in_array($dept, $usr->departemen, true) ? 'selected' : '') : ($usr->departemen == $dept ? 'selected' : '') }}>
                                                                        {{ $dept }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Password Baru <small
                                                                class="text-danger">(Kosongkan jika tidak
                                                                diubah)</small></label>
                                                        <input type="password" name="password" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data user master.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== MODAL TAMBAH USER MASTER ===================== -->
    <div class="modal fade" id="modalTambahUserMaster" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('user.master.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Tambah User Master Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-left">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Role / Hak Akses</label>
                            <div class="single-select-field" data-single-select data-placeholder="-- Pilih Role --">
                                <div class="single-select-box" tabindex="0">
                                    <div class="single-select-selected"></div>
                                    <span class="single-select-caret"></span>
                                </div>
                                <div class="single-select-dropdown" style="display:none;">
                                    @foreach ([['value' => 'super_admin', 'label' => 'Super Admin'], ['value' => 'admin_catering', 'label' => 'Admin Catering'], ['value' => 'petugas_catering', 'label' => 'Petugas Catering']] as $roleOption)
                                        <div class="single-option" data-value="{{ $roleOption['value'] }}">
                                            <span>{{ $roleOption['label'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <select name="role" class="d-none" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="super_admin">Super Admin</option>
                                    <option value="admin_catering">Admin Catering</option>
                                    <option value="petugas_catering">Petugas Catering</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Akses Lokasi Tugas</label>
                            <div class="multi-select-field" data-multi-select data-placeholder="-- Pilih Akses Lokasi --">
                                <div class="multi-select-box" tabindex="0">
                                    <div class="multi-select-selected"></div>
                                    <span class="multi-select-caret"></span>
                                </div>
                                <div class="multi-select-dropdown" style="display:none;">
                                    @foreach (['GCPI 1', 'GCPI 2', 'GCPI 3'] as $lok)
                                        <label class="multi-option">
                                            <input type="checkbox" value="{{ $lok }}">
                                            <span>{{ $lok }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <select name="lokasi[]" class="d-none" multiple>
                                    @foreach (['GCPI 1', 'GCPI 2', 'GCPI 3'] as $lok)
                                        <option value="{{ $lok }}">{{ $lok }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Akses Departemen</label>
                            <div class="multi-select-field" data-multi-select
                                data-placeholder="-- Pilih Akses Departemen --">
                                <div class="multi-select-box" tabindex="0">
                                    <div class="multi-select-selected"></div>
                                    <span class="multi-select-caret"></span>
                                </div>
                                <div class="multi-select-dropdown" style="display:none;">
                                    @foreach (['Customer Service', 'Field_Sales', 'General Affairs', 'HR & GA', 'HR Operations', 'HRPP', 'HSE', 'Indirect Sourcing', 'Industrial Relations', 'Internal Audit', 'IT', 'IT Business Partner', 'IT Infrastructure', 'Legal', 'Operating Company', 'PPIC', 'Product Quality', 'Production', 'Quasi Holding', 'Rewards', 'Secretary', 'Shared', 'Service Center', 'Technic', 'Warehouse Operation'] as $dept)
                                        <label class="multi-option">
                                            <input type="checkbox" value="{{ $dept }}">
                                            <span>{{ $dept }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <select name="departemen[]" class="d-none" multiple>
                                    @foreach (['Customer Service', 'Field_Sales', 'General Affairs', 'HR & GA', 'HR Operations', 'HRPP', 'HSE', 'Indirect Sourcing', 'Industrial Relations', 'Internal Audit', 'IT', 'IT Business Partner', 'IT Infrastructure', 'Legal', 'Operating Company', 'PPIC', 'Product Quality', 'Production', 'Quasi Holding', 'Rewards', 'Secretary', 'Shared', 'Service Center', 'Technic', 'Warehouse Operation'] as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan User Master</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <style>
        .dept-badge-container {
            max-height: 85px;
            overflow-y: auto;
            padding-right: 2px;
        }

        .dept-badge-container::-webkit-scrollbar {
            width: 4px;
        }

        .dept-badge-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }

        .multi-select-field,
        .single-select-field {
            position: relative;
            width: 100%;
        }

        .multi-select-box {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            min-height: calc(1.5em + 0.75rem + 2px);
            max-height: 120px;
            overflow-y: auto;
            border: 1px solid #d1d5db;
            border-radius: 0.35rem;
            background: #fff;
            padding: 0.375rem 0.75rem;
            cursor: pointer;
            box-sizing: border-box;
        }

        .single-select-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: calc(1.5em + 0.75rem + 2px);
            border: 1px solid #d1d5db;
            border-radius: 0.35rem;
            background: #fff;
            padding: 0.375rem 0.75rem;
            cursor: pointer;
            box-sizing: border-box;
        }

        .multi-select-selected,
        .single-select-selected {
            display: flex;
            align-items: center;
            flex: 1;
            padding-right: 8px;
            color: #2d3748;
        }

        .multi-select-selected {
            flex-wrap: wrap;
            gap: 4px;
        }

        .multi-tag {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            background: #3f8fb8;
            color: #fff;
            border-radius: 3px;
            font-size: 12px;
        }

        .multi-tag button {
            background: transparent;
            border: none;
            color: #fff;
            margin-left: 5px;
            cursor: pointer;
        }

        .multi-select-dropdown,
        .single-select-dropdown {
            position: absolute;
            top: calc(100% + 2px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            max-height: 220px;
            overflow-y: auto;
            z-index: 1000;
        }

        .multi-option,
        .single-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #e9ecef;
            background: #fff;
            font-size: 15px;
        }

        .multi-option:hover,
        .single-option:hover {
            background: #edf5ff;
        }

        .multi-option.selected,
        .single-option.selected {
            background: #0d6efd;
            color: #fff;
        }
    </style>

    <script>
        function initMultiSelectField(field) {
            const $field = $(field);
            const $box = $field.find('.multi-select-box');
            const $dropdown = $field.find('.multi-select-dropdown');
            const $selectedArea = $field.find('.multi-select-selected');
            const $hiddenSelect = $field.find('select');
            const $checkboxes = $field.find('input[type="checkbox"]');

            function syncSelection() {
                const selectedValues = [];
                $checkboxes.each(function() {
                    const $checkbox = $(this);
                    const $label = $checkbox.closest('.multi-option');
                    if ($checkbox.is(':checked')) {
                        selectedValues.push($checkbox.val());
                        $label.addClass('selected');
                    } else {
                        $label.removeClass('selected');
                    }
                });

                $hiddenSelect.find('option').prop('selected', false);
                selectedValues.forEach(function(value) {
                    $hiddenSelect.find('option[value="' + value + '"]').prop('selected', true);
                });

                const placeholderText = $field.attr('data-placeholder') || '-- Pilih --';
                $selectedArea.empty();

                if (selectedValues.length === 0) {
                    $selectedArea.append('<span class="multi-placeholder">' + placeholderText + '</span>');
                    return;
                }

                selectedValues.forEach(function(value) {
                    const $tag = $('<span class="multi-tag"></span>');
                    $tag.append(document.createTextNode(value));
                    const $remove = $('<button type="button" aria-label="Hapus">×</button>');
                    $remove.on('click', function(e) {
                        e.stopPropagation();
                        const $targetCheckbox = $field.find('input[type="checkbox"][value="' + value +
                        '"]');
                        $targetCheckbox.prop('checked', false);
                        syncSelection();
                    });
                    $tag.append($remove);
                    $selectedArea.append($tag);
                });
            }

            $box.on('click', function(e) {
                e.stopPropagation();
                $('.multi-select-dropdown, .single-select-dropdown').not($dropdown).hide();
                $dropdown.toggle();
            });

            $dropdown.on('click', function(e) {
                e.stopPropagation();
            });

            $checkboxes.on('change', function() {
                syncSelection();
            });

            syncSelection();
        }

        function initSingleSelectField(field) {
            const $field = $(field);
            const $box = $field.find('.single-select-box');
            const $dropdown = $field.find('.single-select-dropdown');
            const $selectedArea = $field.find('.single-select-selected');
            const $hiddenSelect = $field.find('select');
            const $options = $field.find('.single-option');

            function syncSelection() {
                const selectedValue = $hiddenSelect.val();
                let selectedLabel = '';

                $options.each(function() {
                    const $opt = $(this);
                    if ($opt.attr('data-value') === selectedValue) {
                        $opt.addClass('selected');
                        selectedLabel = $opt.find('span').text();
                    } else {
                        $opt.removeClass('selected');
                    }
                });

                const placeholderText = $field.attr('data-placeholder') || '-- Pilih --';
                $selectedArea.empty();

                if (!selectedValue) {
                    $selectedArea.append('<span class="single-placeholder">' + placeholderText + '</span>');
                    return;
                }

                $selectedArea.append('<span>' + selectedLabel + '</span>');
            }

            const initialVal = $hiddenSelect.val();
            if (initialVal) {
                $options.each(function() {
                    if ($(this).attr('data-value') === initialVal) {
                        $(this).addClass('selected');
                    }
                });
            }

            $box.on('click', function(e) {
                e.stopPropagation();
                $('.multi-select-dropdown, .single-select-dropdown').not($dropdown).hide();
                $dropdown.toggle();
            });

            $dropdown.on('click', function(e) {
                e.stopPropagation();
            });

            $options.on('click', function() {
                const val = $(this).attr('data-value');
                $hiddenSelect.val(val).trigger('change');
                syncSelection();
                $dropdown.hide();
            });

            syncSelection();
        }

        $(document).ready(function() {
            $('.multi-select-field').each(function() {
                initMultiSelectField(this);
            });

            $('.single-select-field').each(function() {
                initSingleSelectField(this);
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.single-select-field, .multi-select-field').length) {
                    $('.multi-select-dropdown, .single-select-dropdown').hide();
                }
            });

            $('#dataTable').DataTable({
                language: {
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari total _MAX_ data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    html: '{!! implode('<br>', $errors->all()) !!}'
                });
            @endif
        });

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success mx-2",
                cancelButton: "btn btn-danger mx-2"
            },
            buttonsStyling: false
        });

        function confirmDelete(id, name) {
            swalWithBootstrapButtons.fire({
                title: "Apakah Anda Yakin?",
                text: "User Master " + name + " akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('.form-delete-' + id).submit();
                }
            });
        }
    </script>
@endpush
