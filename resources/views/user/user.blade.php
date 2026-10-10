@extends('master')

@section('title', 'Data User')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Data User</h1>
        </div>

        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fa-solid fa-house-chimney-user"></i> Daftar Data User
                </h6>
                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahUser">
                    <i class="fas fa-plus mr-1"></i> Tambah User
                </button>
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
                            @forelse ($users as $index => $u)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>
                                        @if ($u->role == 'super_admin')
                                            <span class="badge badge-danger">Super Admin</span>
                                        @elseif($u->role == 'admin_catering')
                                            <span class="badge badge-warning">Admin Catering</span>
                                        @else
                                            <span class="badge badge-success">Petugas Catering</span>
                                        @endif
                                    </td>
                                    <!-- Akses Lokasi -->
                                    <td>
                                        @if ($u->lokasi)
                                            @if (is_array($u->lokasi))
                                                @foreach ($u->lokasi as $lok)
                                                    <span class="badge badge-info mb-1">{{ $lok }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge badge-info">{{ $u->lokasi }}</span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">Semua Lokasi</span>
                                        @endif
                                    </td>

                                    <!-- Akses Departemen (Dibatasi Tinggi & Bisa Scroll jika banyak) -->
                                    <td>
                                        @if ($u->departemen)
                                            <div class="dept-badge-container">
                                                @if (is_array($u->departemen))
                                                    @foreach ($u->departemen as $dept)
                                                        <span class="badge badge-dark mb-1">{{ $dept }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="badge badge-dark">{{ $u->departemen }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge badge-secondary">Semua Departemen</span>
                                        @endif
                                    </td>

                                    <!-- Tombol Aksi Sejajar (Horizontal) -->
                                    <td class="text-center">
                                        @if ($u->email === 'mastergcpi26@godrej.com' && auth()->user()->email !== 'mastergcpi26@godrej.com')
                                            <!-- Jika baris akun master, tapi yang login super admin biasa -> dikunci -->
                                            <span class="badge badge-dark py-2 px-3"
                                                style="font-size: 11px; font-weight: 600;" title="Terkunci untuk Super Admin lain">
                                                <i class="fas fa-lock mr-1"></i> 
                                            </span>
                                        @else
                                            <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                                                <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                                    data-target="#modalEditUser{{ $u->id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form action="{{ route('user.destroy', $u->id) }}" method="POST"
                                                    class="d-inline form-delete-{{ $u->id }} m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger shadow-sm"
                                                        onclick="confirmDelete('{{ $u->id }}', '{{ $u->name }}')"
                                                        title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                <!-- ===================== MODAL EDIT USER ===================== -->
                                @if (!($u->email === 'mastergcpi26@godrej.com' && auth()->user()->email !== 'mastergcpi26@godrej.com'))
                                    <div class="modal fade" id="modalEditUser{{ $u->id }}" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <form action="{{ route('user.update', $u->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold">Edit Data User</h5>
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
                                                                    value="{{ $u->name }}" required>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label class="font-weight-bold">Email</label>
                                                                <input type="email" name="email" class="form-control"
                                                                    value="{{ $u->email }}" required>
                                                            </div>
                                                        </div>

                                                        <!-- Single Select Role -->
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
                                                                        <div class="single-option {{ $u->role == $roleOption['value'] ? 'selected' : '' }}"
                                                                            data-value="{{ $roleOption['value'] }}">
                                                                            <span>{{ $roleOption['label'] }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <select name="role" class="d-none" required>
                                                                    <option value="">-- Pilih Role --</option>
                                                                    <option value="super_admin"
                                                                        {{ $u->role == 'super_admin' ? 'selected' : '' }}>Super
                                                                        Admin</option>
                                                                    <option value="admin_catering"
                                                                        {{ $u->role == 'admin_catering' ? 'selected' : '' }}>
                                                                        Admin Catering</option>
                                                                    <option value="petugas_catering"
                                                                        {{ $u->role == 'petugas_catering' ? 'selected' : '' }}>
                                                                        Petugas Catering</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Multi Select Lokasi -->
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
                                                                                {{ is_array($u->lokasi) ? (in_array($lok, $u->lokasi, true) ? 'checked' : '') : ($u->lokasi == $lok ? 'checked' : '') }}>
                                                                            <span>{{ $lok }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                                <select name="lokasi[]" class="d-none" multiple>
                                                                    @foreach (['GCPI 1', 'GCPI 2', 'GCPI 3'] as $lok)
                                                                        <option value="{{ $lok }}"
                                                                            {{ is_array($u->lokasi) ? (in_array($lok, $u->lokasi, true) ? 'selected' : '') : ($u->lokasi == $lok ? 'selected' : '') }}>
                                                                            {{ $lok }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <small class="form-text text-muted">Pilih lebih dari satu lokasi
                                                                jika perlu. Kosongkan semua jika ingin semua lokasi.</small>
                                                        </div>

                                                        <!-- Multi Select Departemen -->
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
                                                                                {{ is_array($u->departemen) ? (in_array($dept, $u->departemen, true) ? 'checked' : '') : ($u->departemen == $dept ? 'checked' : '') }}>
                                                                            <span>{{ $dept }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                                <select name="departemen[]" class="d-none" multiple>
                                                                    @foreach (['Customer Service', 'Field_Sales', 'General Affairs', 'HR & GA', 'HR Operations', 'HRPP', 'HSE', 'Indirect Sourcing', 'Industrial Relations', 'Internal Audit', 'IT', 'IT Business Partner', 'IT Infrastructure', 'Legal', 'Operating Company', 'PPIC', 'Product Quality', 'Production', 'Quasi Holding', 'Rewards', 'Secretary', 'Shared', 'Service Center', 'Technic', 'Warehouse Operation'] as $dept)
                                                                        <option value="{{ $dept }}"
                                                                            {{ is_array($u->departemen) ? (in_array($dept, $u->departemen, true) ? 'selected' : '') : ($u->departemen == $dept ? 'selected' : '') }}>
                                                                            {{ $dept }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <small class="form-text text-muted">Pilih lebih dari satu
                                                                departemen jika perlu. Kosongkan semua jika ingin semua
                                                                departemen.</small>
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
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== MODAL TAMBAH USER ===================== -->
    <div class="modal fade" id="modalTambahUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Tambah User Baru</h5>
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

                        <!-- Single Select Role -->
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

                        <!-- Multi Select Lokasi -->
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
                            <small class="form-text text-muted">Pilih lebih dari satu lokasi jika perlu. Kosongkan semua
                                jika ingin semua lokasi.</small>
                        </div>

                        <!-- Multi Select Departemen -->
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
                            <small class="form-text text-muted">Pilih lebih dari satu departemen jika perlu. Kosongkan
                                semua jika ingin semua departemen.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
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
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .multi-select-box::-webkit-scrollbar {
            width: 4px;
        }

        .multi-select-box::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
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
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .multi-select-box:focus,
        .multi-select-box:focus-within,
        .single-select-box:focus,
        .single-select-box:focus-within {
            outline: none;
            border-color: #87b5ff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .multi-select-selected {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
            flex: 1;
            padding-right: 8px;
            line-height: 1.4;
            color: #2d3748;
        }

        .single-select-selected {
            display: flex;
            align-items: center;
            flex: 1;
            padding-right: 8px;
            color: #2d3748;
        }

        .multi-tag {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            background: #3f8fb8;
            color: #fff;
            border-radius: 3px;
            font-size: 12px;
            line-height: 1.2;
            font-weight: 500;
            white-space: nowrap;
        }

        .multi-tag button {
            background: transparent;
            border: none;
            color: #fff;
            margin-left: 5px;
            font-size: 14px;
            line-height: 1;
            padding: 0;
            cursor: pointer;
        }

        .multi-placeholder,
        .single-placeholder {
            color: #6c757d;
            font-size: 14px;
        }

        .multi-select-caret,
        .single-select-caret {
            position: relative;
            display: inline-block;
            width: 10px;
            height: 10px;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .multi-select-caret::before,
        .single-select-caret::before {
            content: "";
            position: absolute;
            left: 1px;
            top: 1px;
            width: 7px;
            height: 7px;
            border-right: 2px solid #6c757d;
            border-bottom: 2px solid #6c757d;
            transform: rotate(45deg);
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
            margin: 0;
            cursor: pointer;
            border-bottom: 1px solid #e9ecef;
            background: #fff;
            font-size: 15px;
            color: #1f2d3d;
        }

        .multi-option:last-child,
        .single-option:last-child {
            border-bottom: none;
        }

        .multi-option:hover,
        .single-option:hover {
            background: #edf5ff;
        }

        .multi-option input {
            margin: 0;
            width: 15px;
            height: 15px;
            accent-color: #3a8dde;
        }

        .multi-option.selected,
        .single-option.selected {
            background: #0d6efd;
            color: #fff;
        }

        .multi-option.selected:hover,
        .single-option.selected:hover {
            background: #0b5ed7;
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
                        const $targetCheckbox = $field.find('input[type="checkbox"][value="' + value + '"]');
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

            $field.find('input[type="checkbox"]').on('change', function() {
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

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.single-select-field, .multi-select-field').length) {
                    $('.multi-select-dropdown, .single-select-dropdown').hide();
                }
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
                    html: '{!! implode('<br>', $errors->all()) !!}',
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
                text: "User " + name + " akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('.form-delete-' + id).submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Dibatalkan",
                        text: "Data user aman, tidak jadi dihapus.",
                        icon: "error"
                    });
                }
            });
        }
    </script>
@endpush