@extends('master')

@section('title', 'Activity Log Sistem')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800 font-weight-bold">Activity User</h1>

        <!-- CARD FILTER & PENCARIAN -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 bg-light border-bottom">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter mr-1"></i>Filter & Pencarian History Aktivitas
                </h6>
            </div>
            <form action="{{ route('user.activity.index') }}" method="GET">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control font-weight-bold"
                                value="{{ request('start_date', $startDate ?? \Carbon\Carbon::today()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control font-weight-bold"
                                value="{{ request('end_date', $endDate ?? \Carbon\Carbon::today()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Cari (Nama, Aktivitas, Deskripsi)</label>
                            <input type="text" name="keyword" class="form-control" placeholder="Ketik kata kunci..."
                                value="{{ request('keyword') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary font-weight-bold w-100 shadow-sm">
                                <i class="fas fa-search mr-1"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- CARD TABEL HISTORY -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-1"></i> History Aktivitas User
                    @if (request('start_date') && request('end_date'))
                        <span class="badge badge-secondary ml-2">Periode:
                            {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} s/d
                            {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}</span>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="activityTable" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark font-weight-bold">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">User</th>
                                <th width="12%">Aktivitas</th>
                                <th width="35%">Deskripsi Detail</th>
                                <th width="13%">Tanggal</th>
                                <th width="10%">Jam</th>
                                <th width="10%">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activityLogs as $idx => $log)
                                <tr>
                                    {{-- Menyesuaikan nomor urut agar tetap berlanjut walau berpindah halaman --}}
                                    <td>{{ $activityLogs->firstItem() + $idx }}</td>
                                    <td>
                                        <span
                                            class="font-weight-bold text-dark">{{ $log->user->name ?? 'Sistem / Guest' }}</span><br>
                                        <small class="text-muted">{{ $log->user->email ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if ($log->activity == 'Login')
                                            <span class="badge badge-success">Login</span>
                                        @elseif($log->activity == 'Tambah Data')
                                            <span class="badge badge-primary">Tambah Data</span>
                                        @elseif($log->activity == 'Edit Data')
                                            <span class="badge badge-warning text-dark">Edit Data</span>
                                        @elseif($log->activity == 'Hapus Data')
                                            <span class="badge badge-danger">Hapus Data</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $log->activity }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        <span class="font-weight text-secondary">
                                            {{ $log->created_at->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class=" text-secondary font-weight">
                                            {{ $log->created_at->format('H:i:s') }}
                                        </span>
                                    </td>
                                    <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat aktivitas
                                        tercatat sesuai filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $activityLogs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Nonaktifkan paging dan search bawaan JS karena sudah di-handle oleh Laravel Pagination
            $('#activityTable').DataTable({
                paging: false,
                info: false,
                searching: false,
                order: [
                    [4, 'desc'],
                    [5, 'desc']
                ]
            });
        });
    </script>
@endpush
