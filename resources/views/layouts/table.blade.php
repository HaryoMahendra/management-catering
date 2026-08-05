<div class="card shadow mb-4 border-0">
    <div class="card-header py-3 bg-light border-bottom">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-line mr-1"></i> Ringkasan Monitoring Catering
        </h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-dark font-weight-bold">
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Karyawan</th>
                        <th>Sudah Ambil</th>
                        <th>Belum Ambil</th>
                        <th>Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ringkasanMonitoring as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ number_format($row->total_karyawan) }} Karyawan</td>
                        <td>
                            <span class="text-success font-weight-bold">{{ number_format($row->sudah_mengambil) }}</span>
                        </td>
                        <td>
                            <span class="text-danger font-weight-bold">{{ number_format($row->belum_mengambil) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $row->persentase == 100 ? 'badge-success' : 'badge-info' }} p-2">
                                {{ $row->persentase }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada riwayat data pengambilan catering.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "search": "Cari Data:"
                },
                "pageLength": 10,
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endpush