@extends('master')

@section('title', 'Profile Saya')

@section('content')
    <!-- Header Judul Halaman -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Profile Saya</h1>
    </div>

    <div class="row">
        <!-- CARD KIRI: FOTO & INFORMASI SINGKAT -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 text-center py-4">
                <div class="card-body">
                    <!-- TAMPILAN FOTO PROFILE (Gunakan Foto User / Default) -->
                    <div class="position-relative d-inline-block mb-3">
                        <img id="profileImagePreview" 
                            class="img-profile rounded-circle shadow-sm border"
                            src="{{ Auth::user()->foto ? asset('storage/profile/' . Auth::user()->foto) : asset('sbadmin/img/undraw_profile.svg') }}" 
                            width="140" height="140" style="object-fit: cover;">
                    </div>
                    
                    <h5 class="font-weight-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-2">{{ Auth::user()->email }}</p>
                    
                    <span class="badge badge-primary px-3 py-2 text-uppercase font-weight-bold" style="font-size: 11px;">
                        {{ str_replace('_', ' ', Auth::user()->role ?? 'User') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- CARD KANAN: FORM EDIT PROFILE, FOTO & PASSWORD -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3 bg-light border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-cog mr-2"></i>Edit Informasi Profile
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Tambahkan enctype="multipart/form-data" -->
                    <form id="formProfile" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- INPUT FOTO PROFILE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Foto Profile</label>
                            <input type="file" name="foto" id="fotoInput" class="form-control-file border p-2 rounded w-100" accept="image/*">
                            <small class="text-muted">* Format: JPG, JPEG, PNG, GIF (Maks. 2MB)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <hr class="my-4">
                        <h6 class="font-weight-bold text-dark mb-3">Ubah Password (Opsional)</h6>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Preview Foto Instan Sebelum di-Upload
            $('#fotoInput').on('change', function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profileImagePreview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Mixin Style SweetAlert2
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-primary mx-2",
                    cancelButton: "btn btn-secondary mx-2"
                },
                buttonsStyling: false
            });

            // Konfirmasi Sebelum Submit Form Profile
            $('#formProfile').on('submit', function(e) {
                e.preventDefault();
                let form = this;

                swalWithBootstrapButtons.fire({
                    title: "Simpan Perubahan Profile?",
                    text: "Pastikan data profile dan foto yang diunggah sudah benar!",
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

            // Pop-up Toast Sukses
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2500
                });
            @endif

            // Pop-up Alert Error Validasi
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memperbarui!',
                    html: `@foreach ($errors->all() as $error)
                            <p class="mb-1 text-left">• {{ $error }}</p>
                           @endforeach`,
                    confirmButtonText: 'Tutup'
                });
            @endif
        });
    </script>
@endpush