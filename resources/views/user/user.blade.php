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
                  <i class="fa-solid fa-house-chimney-user"></i> Daftar User Sistem
            </h6>
            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahUser">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark font-weight-bold">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $u)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @if($u->role == 'super_admin')
                                    <span class="badge badge-danger">Super Admin</span>
                                @elseif($u->role == 'admin_catering')
                                    <span class="badge badge-warning">Admin Catering</span>
                                @else
                                    <span class="badge badge-success">Petugas Catering</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditUser{{ $u->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('user.destroy', $u->id) }}" method="POST" class="d-inline form-delete-{{ $u->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $u->id }}', '{{ $u->name }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- ===================== MODAL EDIT USER ===================== -->
                        <div class="modal fade" id="modalEditUser{{ $u->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('user.update', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data User</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Role / Hak Akses</label>
                                                <select name="role" class="form-control" required>
                                                    <option value="super_admin" {{ $u->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                                    <option value="admin_catering" {{ $u->role == 'admin_catering' ? 'selected' : '' }}>Admin Catering</option>
                                                    <option value="petugas_catering" {{ $u->role == 'petugas_catering' ? 'selected' : '' }}>Petugas Catering</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Password Baru <small class="text-danger">(Kosongkan jika tidak diubah)</small></label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data user.</td>
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
    <div class="modal-dialog" role="document">
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role / Hak Akses</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="admin_catering">Admin Catering</option>
                            <option value="petugas_catering">Petugas Catering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
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

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: {
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
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

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan!',
                html: '{!! implode("<br>", $errors->all()) !!}',
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