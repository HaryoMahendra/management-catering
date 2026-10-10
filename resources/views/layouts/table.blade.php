<!-- Filter Data Monitoring -->
<div class="card shadow mb-4 border-0">
    <div class="card-header py-3 bg-light border-bottom">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-1"></i> Filter Data Monitoring Catering
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('dashboard') }}" method="GET" class="form-row align-items-end">
            <!-- Tanggal Booking -->
            <div class="form-group col-md-3 mb-md-0">
                <label class="small font-weight-bold text-muted">Tanggal Booking</label>
                <input type="date" name="tanggal_booking" class="form-control"
                    value="{{ request('tanggal_booking', date('Y-m-d')) }}">
            </div>
            <!-- Tanggal Scan -->
            <div class="form-group col-md-3 mb-md-0">
                <label class="small font-weight-bold text-muted">Tanggal Scan</label>
                <input type="date" name="tanggal_scan" class="form-control" value="{{ request('tanggal_scan') }}">
            </div>

            <!-- Schedule -->
            <div class="form-group col-md-3 mb-md-0">
                <label class="small font-weight-bold text-muted">Schedule</label>
                <select name="schedule" class="form-control">
                    <option value="">-- Semua --</option>
                    <option value="Shift 1" {{ request('schedule') == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                    <option value="Shift 2" {{ request('schedule') == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
                    <option value="Shift 3" {{ request('schedule') == 'Shift 3' ? 'selected' : '' }}>Shift 3</option>
                    <option value="Non Shift" {{ request('schedule') == 'Non Shift' ? 'selected' : '' }}>Non Shift
                    </option>
                </select>
            </div>

            <!-- Tombol Tampilkan -->
            <div class="form-group col-md-3 mb-md-0">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search mr-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Card Tabel Ringkasan Monitoring -->
<div class="card shadow mb-4 border-0">
    <div class="card-header py-3 bg-light border-bottom">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-line mr-1"></i> Data Ringkasan Monitoring Catering
        </h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-dark font-weight-bold">
                    <tr>
                        <th>Tanggal Booking</th>
                        <th>Schedule</th>
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
                            <td>
                                <span class="badge badge-secondary p-2">{{ $row->schedule }}</span>
                            </td>
                            <td>{{ number_format($row->total_karyawan) }} Orang</td>
                            <td>
                                <span
                                    class="text-success font-weight-bold">{{ number_format($row->sudah_mengambil) }}</span>
                            </td>
                            <td>
                                <span
                                    class="text-danger font-weight-bold">{{ number_format($row->belum_mengambil) }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $row->persentase == 100 ? 'badge-success' : 'badge-info' }} p-2">
                                    {{ $row->persentase }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada riwayat data pengambilan catering yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
