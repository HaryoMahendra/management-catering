<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\PengambilanCatering;
use App\Imports\KaryawanImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CateringController extends Controller
{
    private function normalizeAccessValues($value)
    {
        if (is_array($value)) {
            $value = array_values(array_filter($value, fn($item) => $item !== null && $item !== ''));
            return array_map('strval', $value);
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return [];
            }

            $parts = preg_split('/[;,|]/', $trimmed);
            $parts = array_map('trim', $parts);
            $parts = array_values(array_filter($parts, fn($item) => $item !== ''));

            return array_map('strval', $parts);
        }

        return [];
    }

    private function applyUserAccessFilter($query, $user)
    {
        if (!$user || $user->role === 'super_admin') {
            return $query;
        }

        $adminLocations = $this->normalizeAccessValues($user->location ?? $user->lokasi ?? null);
        $adminDepartments = $this->normalizeAccessValues($user->departemen ?? $user->dept ?? null);

        $adminLocations = array_map('strtolower', $adminLocations);
        $adminDepartments = array_map('strtolower', $adminDepartments);

        if (!empty($adminLocations) && !in_array('semua lokasi', $adminLocations, true)) {
            if (Schema::hasColumn('karyawans', 'location')) {
                $query->where(function ($q) use ($adminLocations) {
                    $q->whereIn(DB::raw('LOWER(location)'), $adminLocations);
                });
            }
        }

        if (!empty($adminDepartments) && !in_array('semua departemen', $adminDepartments, true)) {
            if (Schema::hasColumn('karyawans', 'departemen')) {
                $query->where(function ($q) use ($adminDepartments) {
                    $q->whereIn(DB::raw('LOWER(departemen)'), $adminDepartments);
                });
            }
        }

        return $query;
    }

   // Dashboard dengan Filter Tanggal Booking, Tanggal Scan, Schedule (Case-Insensitive), dan Status
    // Dashboard dengan Filter Tanggal Booking, Tanggal Scan, Schedule (Case-Insensitive), dan Status
    public function dashboard(Request $request)
    {
        // Ubah agar default tanggal_booking adalah hari ini jika tidak ada input dari request
        $tanggalBooking = $request->input('tanggal_booking', Carbon::today()->toDateString());
        $tanggalScan    = $request->input('tanggal_scan');
        $filterSchedule = $request->input('schedule');
        $filterStatus   = $request->input('status', 'semua');

        // Normalisasi input filter schedule ke huruf kecil agar fleksibel
        $filterScheduleLower = $filterSchedule ? strtolower(trim($filterSchedule)) : null;

        // 1. Query Data Karyawan pada Tanggal Booking, lalu buang duplikat NIK (unique)
        $user = Auth::user();
        $query = Karyawan::query();
        $query = $this->applyUserAccessFilter($query, $user);

        if ($tanggalBooking) {
            $query->whereDate('created_at', $tanggalBooking);
        }

        if ($filterScheduleLower) {
            $query->where(DB::raw('LOWER(schedule)'), $filterScheduleLower);
        }

        // Ambil data unik berdasarkan employee_no agar totalnya menjadi 929
        $karyawansUnik = $query->get()->unique('employee_no');

        // NORMALISASI NAMA SHIFT TERLEBIH DAHULU SEBELUM DI-GROUPING
        $karyawansUnik = $karyawansUnik->map(function($item) {
            $sched = trim($item->schedule ?? $item->shift ?? 'Non Shift');
            $lower = strtolower($sched);

            if (in_array($lower, ['non shift', 'non-shift', 'nonshift'])) {
                $item->standard_schedule = 'Non Shift';
            } elseif (in_array($lower, ['shift 1', 'shift1', '1'])) {
                $item->standard_schedule = 'Shift 1';
            } elseif (in_array($lower, ['shift 2', 'shift2', '2'])) {
                $item->standard_schedule = 'Shift 2';
            } elseif (in_array($lower, ['shift 3', 'shift3', '3'])) {
                $item->standard_schedule = 'Shift 3';
            } else {
                $item->standard_schedule = ucwords($sched);
            }

            return $item;
        });

        // Kelompokkan berdasarkan standard_schedule yang sudah seragam
        $groupedKaryawans = $karyawansUnik->groupBy('standard_schedule');

        $ringkasanMonitoring = collect();

        foreach ($groupedKaryawans as $normalizedShift => $items) {
            $tglScanTarget = $tanggalScan ?: $tanggalBooking;

            // Hitung berapa banyak karyawan unik pada shift ini yang sudah scan
            $karyawanIds = $items->pluck('id');
            $sudahAmbilCount = PengambilanCatering::whereDate('tanggal', $tglScanTarget)
                ->whereIn('karyawan_id', $karyawanIds)
                ->count();

            $totalBooking = $items->count();
            $belumAmbil   = max(0, $totalBooking - $sudahAmbilCount);
            $persen       = $totalBooking > 0 ? round(($sudahAmbilCount / $totalBooking) * 100, 1) : 0;

            $rowObj = new \stdClass();
            $rowObj->tanggal         = $tanggalBooking;
            $rowObj->schedule        = $normalizedShift;
            $rowObj->total_karyawan  = $totalBooking;
            $rowObj->sudah_mengambil = $sudahAmbilCount;
            $rowObj->belum_mengambil = $belumAmbil;
            $rowObj->persentase      = $persen;

            $ringkasanMonitoring->push($rowObj);
        }

        // 2. Filter status pada tabel ringkasan
        if ($filterStatus == 'sudah') {
            $ringkasanMonitoring = $ringkasanMonitoring->filter(fn($row) => $row->sudah_mengambil > 0);
        } elseif ($filterStatus == 'belum') {
            $ringkasanMonitoring = $ringkasanMonitoring->filter(fn($row) => $row->sudah_mengambil == 0);
        }

        // 3. Hitung Total untuk Card Utama Berdasarkan Hasil Filter Unik
        $totalKaryawan  = $ringkasanMonitoring->sum('total_karyawan');
        $sudahMengambil = $ringkasanMonitoring->sum('sudah_mengambil');
        $belumMengambil = $ringkasanMonitoring->sum('belum_mengambil');
        $persentase     = $totalKaryawan > 0 ? round(($sudahMengambil / $totalKaryawan) * 100, 1) : 0;

        // 4. Hitung Total Booking per Shift Berdasarkan Hasil Filter Aktif
        $totalNonShift = $ringkasanMonitoring->where('schedule', 'Non Shift')->sum('total_karyawan');
        $totalShift1   = $ringkasanMonitoring->where('schedule', 'Shift 1')->sum('total_karyawan');
        $totalShift2   = $ringkasanMonitoring->where('schedule', 'Shift 2')->sum('total_karyawan');
        $totalShift3   = $ringkasanMonitoring->where('schedule', 'Shift 3')->sum('total_karyawan');

        return view('dashboard', compact(
            'totalKaryawan',
            'sudahMengambil',
            'belumMengambil',
            'persentase',
            'totalNonShift',
            'totalShift1',
            'totalShift2',
            'totalShift3',
            'ringkasanMonitoring',
            'tanggalBooking',
            'tanggalScan',
            'filterSchedule',
            'filterStatus'
        ));
    }

    // Halaman Scan QR
   public function scanIndex()
    {
        if (Auth::check() && Auth::user()->role === 'admin_catering') {
            abort(403, 'Unauthorized action.');
        }

        return view('scan');
    }

    // Process Scan QR
    public function processScan(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'admin_catering') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak! Admin Catering tidak diizinkan melakukan scan QR.'
            ], 403);
        }
        $employeeNo = $request->input('employee_no');

        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i');
        $today = $now->toDateString();

        $targetTanggalCatering = $today;
        if ($currentTime < '07:00') {
            $targetTanggalCatering = $now->copy()->subDay()->toDateString();
        }

        // Ambil semua data karyawan dengan employee_no tersebut pada tanggal catering target
        $karyawanList = Karyawan::where('employee_no', $employeeNo)
            ->whereDate('created_at', $targetTanggalCatering)
            ->get();

        if ($karyawanList->isEmpty()) {
            $checkAny = Karyawan::where('employee_no', $employeeNo)->first();
            if (!$checkAny) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee No / QR Code tidak terdaftar!'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => "Gagal! Tidak ada jadwal booking catering untuk tanggal {$targetTanggalCatering} dengan nomor karyawan tersebut."
            ]);
        }

        $karyawan = null;

        // Cari baris data yang jadwal/shift-nya SESUAI dengan jam saat ini
        foreach ($karyawanList as $k) {
            $userSchedule = strtolower(trim($k->schedule ?? $k->shift ?? ''));
            
            $isAllowedTime = false;
            if (in_array($userSchedule, ['non shift', 'non-shift', 'nonshift', 'general'])) {
                $isAllowedTime = ($currentTime >= '08:00' && $currentTime < '17:00');
            } elseif (in_array($userSchedule, ['shift 1', 'shift1', '1'])) {
                $isAllowedTime = ($currentTime >= '07:00' && $currentTime < '15:00');
            } elseif (in_array($userSchedule, ['shift 2', 'shift2', '2'])) {
                $isAllowedTime = ($currentTime >= '15:00' && $currentTime < '23:00');
            } elseif (in_array($userSchedule, ['shift 3', 'shift3', '3'])) {
                $isAllowedTime = ($currentTime >= '23:00' || $currentTime < '07:00');
            } else {
                // Jika shift tidak dikenali, izinkan secara default
                $isAllowedTime = true;
            }

            if ($isAllowedTime) {
                // Cek apakah shift ini sudah pernah scan sebelumnya
                $alreadyScanned = PengambilanCatering::where('karyawan_id', $k->id)
                    ->whereDate('tanggal', $targetTanggalCatering)
                    ->exists();

                if (!$alreadyScanned) {
                    $karyawan = $k;
                    break; // Ketemu shift yang aktif dan belum diambil
                } else {
                    // Jika shift yang sedang aktif ini sudah terlanjur diambil, berikan info spesifik
                    return response()->json([
                        'success' => false,
                        'message' => "Karyawan (" . ($k->name ?? $k->nama) . " - Schedule: {$k->schedule}) sudah mengambil catering untuk shift ini pada tanggal {$targetTanggalCatering}!"
                    ]);
                }
            }
        }

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => "Gagal! Waktu saat ini ({$currentTime}) tidak sesuai dengan rentang jam shift manapun yang terdaftar untuk karyawan ini."
            ]);
        }

        // Catat pengambilan catering untuk shift spesifik tersebut
        PengambilanCatering::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => $targetTanggalCatering,
            'waktu_scan'  => $now->format('H:i:s')
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Berhasil! Silakan ambil porsi catering untuk ' . ($karyawan->schedule ?? 'Shift Ini') . '.',
            'karyawan' => [
                'name'             => $karyawan->name ?? $karyawan->nama ?? '-',
                'nama'             => $karyawan->name ?? $karyawan->nama ?? '-',
                'employee_no'      => $karyawan->employee_no ?? $karyawan->nif ?? '-',
                'departemen'       => $karyawan->departemen ?? $karyawan->dept ?? '-',
                'location'         => $karyawan->location ?? $karyawan->lokasi ?? '-',
                'lokasi'           => $karyawan->location ?? $karyawan->lokasi ?? '-',
                'schedule'         => $karyawan->schedule ?? '-',
                'shift'            => $karyawan->schedule ?? '-',
            ]
        ]);
    }
    
   // Halaman Booking Catering (Filtered by Admin Location & Department)
   // Halaman Booking Catering (Filtered by Admin Location & Department)
    // Halaman Booking Catering (Filtered by Admin Location & Department)
    public function bookingIndex(Request $request)
    {
        $user = Auth::user();

        // Ambil input dari request, jika kosong default-kan tanggal ke hari ini
        $tanggalFilter    = $request->input('tanggal', Carbon::today()->toDateString());
        $departemenFilter = $request->input('departemen');
        $scheduleFilter   = $request->input('schedule');
        $locationFilter   = $request->input('location');
        $statusFilter     = $request->input('status_catering');

        $query = Karyawan::query();
        $query = $this->applyUserAccessFilter($query, $user);

        // Filter berdasarkan tanggal booking (created_at)
        if ($tanggalFilter) {
            $query->whereDate('created_at', $tanggalFilter);
        }

        // Filter berdasarkan Departemen
        if ($request->filled('departemen')) {
            $query->where('departemen', $departemenFilter);
        }

        // Filter berdasarkan Schedule
        if ($request->filled('schedule')) {
            $query->where('schedule', $scheduleFilter);
        }

        // Filter berdasarkan Location
        if ($request->filled('location')) {
            $query->where('location', $locationFilter);
        }

        // AMBIL DATA & PASTIKAN HANYA UNIK BERDASARKAN EMPLOYEE_NO (Mencegah duplikat tampil)
        $karyawans = $query->get()->unique('employee_no');

        // Ambil log pengambilan berdasarkan tanggal filter
        $pengambilanLogs = PengambilanCatering::whereDate('tanggal', $tanggalFilter)
            ->get()
            ->groupBy('karyawan_id');

        $karyawans = $karyawans->map(function ($karyawan) use ($pengambilanLogs) {
            $logs = $pengambilanLogs->get($karyawan->id);

            // Cek apakah ada data log scan pada tanggal tersebut
            if ($logs && $logs->count() > 0) {
                $karyawan->status_catering = 'Sudah';
            } else {
                $karyawan->status_catering = 'Belum';
            }
            
            $sched = trim($karyawan->schedule ?? $karyawan->shift ?? '-');
            $lowerSched = strtolower($sched);
            
            if (in_array($lowerSched, ['non shift', 'non-shift', 'nonshift'])) {
                $karyawan->normalized_schedule = 'Non Shift';
            } elseif (in_array($lowerSched, ['shift 1', 'shift1', '1'])) {
                $karyawan->normalized_schedule = 'Shift 1';
            } elseif (in_array($lowerSched, ['shift 2', 'shift2', '2'])) {
                $karyawan->normalized_schedule = 'Shift 2';
            } elseif (in_array($lowerSched, ['shift 3', 'shift3', '3'])) {
                $karyawan->normalized_schedule = 'Shift 3';
            } else {
                $karyawan->normalized_schedule = ucwords($sched);
            }

            return $karyawan;
        });

        // Filter tambahan untuk Status Catering (Sudah / Belum) pada Collection
        if ($request->filled('status_catering')) {
            $karyawans = $karyawans->filter(function ($item) use ($statusFilter) {
                return strtolower($item->status_catering) === strtolower($statusFilter);
            });
        }

        return view('booking', compact('karyawans', 'tanggalFilter'));
    }

    // Proses Import Data Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new KaryawanImport, $request->file('file_excel'));

        return redirect()->back()->with('success', 'Data Karyawan berhasil di-import dari Excel!');
    }

    // Update Data Karyawan
    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $updateData = $request->only([
            'employee_no',
            'name',
            'employment_under',
            'departemen',
            'indonesia_level',
            'location',
            'schedule',
            'no_hp'
        ]);

        if ($request->has('lokasi') && !$request->has('location')) {
            $updateData['location'] = $request->input('lokasi');
        }

        // Tangkap input tanggal baru jika diisi dari form edit
        if ($request->filled('tanggal')) {
            $updateData['created_at'] = Carbon::parse($request->input('tanggal'))->format('Y-m-d H:i:s');
        }

        $karyawan->update($updateData);

        if ($request->has('status_catering')) {
            $targetTanggal = $request->filled('tanggal')
                ? Carbon::parse($request->input('tanggal'))->toDateString()
                : Carbon::parse($karyawan->created_at)->toDateString();

            $status = strtolower($request->input('status_catering'));

            if ($status == 'sudah') {
                PengambilanCatering::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'tanggal'     => $targetTanggal,
                    ],
                    [
                        'waktu_scan'  => Carbon::now('Asia/Jakarta')->format('H:i:s')
                    ]
                );
            } else {
                PengambilanCatering::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $targetTanggal)
                    ->delete();
            }
        }

        return redirect()->back()->with('success', 'Data karyawan dan tanggal booking berhasil diperbarui!');
    }

    // Hapus Data Karyawan
    public function destroyKaryawan($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->back()->with('success', 'Data karyawan berhasil dihapus!');
    }

    public function destroyMultipleKaryawan(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:karyawans,id',
        ]);

        // Ambil data satu persatu lalu hapus menggunakan instance model 
        // agar ActivityObserver otomatis terpanggil untuk setiap baris data
        $karyawans = Karyawan::whereIn('id', $request->input('ids'))->get();
        
        foreach ($karyawans as $karyawan) {
            $karyawan->delete();
        }

        return redirect()->back()->with('success', 'Data karyawan terpilih berhasil dihapus!');
    }

    // Halaman WhatsApp QR / Catering Card
    public function whatsappIndex()
    {
        $user = Auth::user();
        $query = Karyawan::whereNotNull('no_hp');
        $query = $this->applyUserAccessFilter($query, $user);

        $karyawans = $query->get();

        return view('whatsapp', compact('karyawans'));
    }

    // Halaman Laporan Catering
    public function laporanIndex(Request $request)
    {
        $user = Auth::user();

        $startDate    = $request->input('start_date', Carbon::today()->toDateString());
        $endDate      = $request->input('end_date', Carbon::today()->toDateString());
        $filterStatus = $request->input('status', 'semua');

        $query = Karyawan::query();
        $query = $this->applyUserAccessFilter($query, $user);
        $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        
        // TAMBAHKAN unique('employee_no') DISINI AGAR TOTALNYA IKUT MENJADI 929
        $allKaryawans = $query->get()->unique('employee_no');

        $pengambilanLogs = PengambilanCatering::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('karyawan_id');

        $karyawans = $allKaryawans->map(function ($karyawan) use ($pengambilanLogs) {
            $logs = $pengambilanLogs->get($karyawan->id);

            if ($logs && $logs->count() > 0) {
                $lastLog                 = $logs->first();
                $karyawan->status_catering = 'Sudah';
                $karyawan->waktu_scan      = $lastLog->waktu_scan;
                $karyawan->tanggal_scan    = $lastLog->tanggal;
            } else {
                $karyawan->status_catering = 'Belum';
                $karyawan->waktu_scan      = '-';
                $karyawan->tanggal_scan    = '-';
            }

            return $karyawan;
        });

        if ($filterStatus == 'sudah') {
            $karyawans = $karyawans->where('status_catering', 'Sudah');
        } elseif ($filterStatus == 'belum') {
            $karyawans = $karyawans->where('status_catering', 'Belum');
        }

        $totalKaryawan = $allKaryawans->count();
        $totalSudah    = $karyawans->where('status_catering', 'Sudah')->count();
        $totalBelum    = $karyawans->where('status_catering', 'Belum')->count();

        return view('laporan', compact(
            'karyawans',
            'startDate',
            'endDate',
            'filterStatus',
            'totalKaryawan',
            'totalSudah',
            'totalBelum'
        ));
    }

    // Halaman Card Catering
    public function cardCatering(Request $request)
    {
        $user = Auth::user();
        $query = Karyawan::query();
        $query = $this->applyUserAccessFilter($query, $user);

        if ($request->filled('keyword')) {
            $keyword = trim($request->input('keyword'));
            $query->where('employee_no', $keyword);
        }

        $karyawans = $query->get();

        return view('cardcatering', compact('karyawans'));
    }

    // Proses Tambah Data Karyawan Manual (Berdasarkan NIK)
    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'employee_no'      => 'required|string|max:255',
            'name'             => 'required|string|max:255',
            'employment_under' => 'nullable|string|max:255',
            'departemen'       => 'nullable|string|max:255',
            'indonesia_level'  => 'nullable|string|max:255',
            'location'         => 'required|string|max:255',
            'schedule'         => 'required|string|max:255',
            'status_catering'  => 'required|string|in:sudah,belum',
        ]);

        $targetTanggal = Carbon::today()->toDateString();

        // Cari apakah NIK tersebut sudah ada di hari yang sama
        $karyawan = Karyawan::where('employee_no', $request->employee_no)
            ->whereDate('created_at', $targetTanggal)
            ->first();

        if ($karyawan) {
            // Jika sudah ada, update data yang ada (mengganti shift/lokasi/status jika diubah)
            $karyawan->update([
                'name'             => strtoupper($request->name),
                'employment_under' => $request->employment_under,
                'departemen'       => $request->departemen,
                'indonesia_level'  => $request->indonesia_level,
                'location'         => $request->location,
                'schedule'         => $request->schedule,
            ]);
        } else {
            // Jika belum ada, buat data baru
            $karyawan = Karyawan::create([
                'employee_no'      => $request->employee_no,
                'name'             => strtoupper($request->name),
                'employment_under' => $request->employment_under,
                'departemen'       => $request->departemen,
                'indonesia_level'  => $request->indonesia_level,
                'location'         => $request->location,
                'schedule'         => $request->schedule,
            ]);
        }

        // Sinkronisasi status pengambilan catering
        $status = strtolower($request->input('status_catering'));

        if ($status == 'sudah') {
            PengambilanCatering::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'tanggal'     => $targetTanggal,
                ],
                [
                    'waktu_scan'  => Carbon::now('Asia/Jakarta')->format('H:i:s')
                ]
            );
        } else {
            PengambilanCatering::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $targetTanggal)
                ->delete();
        }

        return redirect()->route('booking.index')->with('success', 'Data karyawan berhasil disimpan!');
    }

    // Halaman Activity Log Sistem dengan Filter Rentang Tanggal dan Pencarian Keyword
    public function activityIndex(Request $request)
    {
        if (Auth::user()->email !== 'mastergcpi26@godrej.com') {
            abort(403, 'Unauthorized action.');
        }

        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());
        $keyword   = $request->input('keyword');

        $query = \App\Models\ActivityLog::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        }

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('activity', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('ip_address', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function($userQuery) use ($keyword) {
                      $userQuery->where('name', 'like', "%{$keyword}%")
                                ->orWhere('email', 'like', "%{$keyword}%");
                  });
            });
        }

        $activityLogs = $query->latest()->paginate(10)->appends($request->all());

        return view('user.activity', compact('activityLogs', 'startDate', 'endDate', 'keyword'));
    }

    
}