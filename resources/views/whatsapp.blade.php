@extends('master')

@section('title', 'WhatsApp Blast QR')

@section('content')
    <!-- Header Judul Halaman -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">WhatsApp Blast QR Code</h1>
    </div>

    <!-- Form WA Blast -->
    <form action="{{ route('whatsapp.send') }}" method="POST">
        @csrf
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fab fa-whatsapp mr-2"></i>Daftar Karyawan Penerima QR
                </h6>
                <button type="submit" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-paper-plane mr-1"></i> Blast QR ke Nomor WA Dipilih
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="dataTableWA" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark font-weight-bold">
                            <tr>
                                <th width="5%" class="text-center">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th width="5%">No</th>
                                <th>NIF</th>
                                <th>Nama Karyawan</th>
                                <th>Departemen</th>
                                <th>No. WhatsApp</th>
                                {{-- <th>Link Target Scan</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawans as $index => $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="karyawan_ids[]" value="{{ $item->id }}"
                                            class="check-item">
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-weight-bold text-dark">{{ $item->nif }}</td>
                                    <td class="text-uppercase">{{ $item->nama }}</td>
                                    <td>{{ $item->departemen ?? '-' }}</td>
                                    <td>
                                        {{ $item->no_hp }}
                                    </td>
                                    {{-- <td>
                                        <a href="{{ route('scan.index', ['nif' => $item->nif]) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold">
                                            <i class="fas fa-external-link-alt mr-1"></i> Buka Link QR
                                        </a>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data karyawan dengan
                                        Nomor WhatsApp terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <!-- DataTables JavaScript & CSS Plugin -->
    <link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTableWA').DataTable({
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
                    "infoEmpty": "Tidak ada data tersedia",
                    "search": "Cari Karyawan / NIF / WA:",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Selanjutnya"
                    }
                },
                "pageLength": 10
            });

            $('#checkAll').click(function() {
                $('.check-item').prop('checked', this.checked);
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Proses Selesai!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#1cc88a'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#e74a3b'
                });
            @endif
        });
    </script>
@endpush
