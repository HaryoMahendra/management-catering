<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\PengambilanCatering;
use App\Imports\KaryawanImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CateringController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        // 1. Ringkasan Widget Atas (Hari Ini)
        $totalKaryawan = Karyawan::count();
        $sudahMengambil = PengambilanCatering::whereDate('tanggal', $today)->count();
        $belumMengambil = max(0, $totalKaryawan - $sudahMengambil);
        $persentase     = $totalKaryawan > 0 ? round(($sudahMengambil / $totalKaryawan) * 100) : 0;

        // 2. Ringkasan Tabel Riwayat Monitoring (Dinamis per Tanggal)
        $ringkasanMonitoring = PengambilanCatering::selectRaw('
                tanggal,
                COUNT(DISTINCT karyawan_id) as sudah_mengambil
            ')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) {
                // Total karyawan yang sudah terdaftar pada/sebelum tanggal tersebut
                $totalKaryawanPadaTanggal = Karyawan::whereDate('created_at', '<=', $item->tanggal)->count();

                // Fallback jika data created_at tidak terikat
                if ($totalKaryawanPadaTanggal == 0) {
                    $totalKaryawanPadaTanggal = Karyawan::count();
                }

                $item->total_karyawan  = $totalKaryawanPadaTanggal;
                $item->belum_mengambil = max(0, $totalKaryawanPadaTanggal - $item->sudah_mengambil);
                $item->persentase      = $totalKaryawanPadaTanggal > 0
                    ? round(($item->sudah_mengambil / $totalKaryawanPadaTanggal) * 100, 1)
                    : 0;

                return $item;
            });

        return view('dashboard', compact(
            'totalKaryawan',
            'sudahMengambil',
            'belumMengambil',
            'persentase',
            'ringkasanMonitoring'
        ));
    }

    // Halaman Scan QR
    public function scanIndex()
    {
        return view('scan');
    }

    // Process Scan QR dengan Validasi Shift, Tanggal Lintas Hari, & Validasi Tanggal Booking
    public function processScan(Request $request)
    {
        $nif = $request->input('nif');
        
        // Waktu realtime server (Asia/Jakarta)
        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i');
        $today = $now->toDateString();

        // 1. Cari data karyawan
        $karyawan = Karyawan::where('nif', $nif)->first();
        if (!$karyawan) {
            return response()->json([
                'success' => false, 
                'message' => 'NIK / QR Code tidak terdaftar!'
            ]);
        }

        $userShift = strtolower(trim($karyawan->shift ?? ''));
        $isNonShift = in_array($userShift, ['non shift', 'non-shift', 'nonshift', 'general']);
        $isShift1   = in_array($userShift, ['shift 1', 'shift1', '1']);
        $isShift2   = in_array($userShift, ['shift 2', 'shift2', '2']);
        $isShift3   = in_array($userShift, ['shift 3', 'shift3', '3']);

        $isAllowed = false;
        $shiftScheduleInfo = "";
        $targetTanggalCatering = $today; // Default tanggal pencatatan porsi

        // 2. Validasi Jadwal Shift & Penentuan Tanggal Porsi Catering
        if ($isNonShift) {
            // Non Shift: 08:00 - 17:00
            $isAllowed = ($currentTime >= '08:00' && $currentTime < '17:00');
            $shiftScheduleInfo = "08:00 - 17:00";
        } elseif ($isShift1) {
            // Shift 1: 07:00 - 15:00
            $isAllowed = ($currentTime >= '07:00' && $currentTime < '15:00');
            $shiftScheduleInfo = "07:00 - 15:00";
        } elseif ($isShift2) {
            // Shift 2: 15:00 - 23:00
            $isAllowed = ($currentTime >= '15:00' && $currentTime < '23:00');
            $shiftScheduleInfo = "15:00 - 23:00";
        } elseif ($isShift3) {
            // Shift 3: 23:00 - 07:00 (Melintasi Tengah Malam)
            $isAllowed = ($currentTime >= '23:00' || $currentTime < '07:00');
            $shiftScheduleInfo = "23:00 - 07:00";

            // Jika scan dilakukan antara jam 00:00 - 06:59 pagi,
            // porsi catering milik Shift 3 tanggal kemarin
            if ($currentTime < '07:00') {
                $targetTanggalCatering = $now->copy()->subDay()->toDateString();
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Shift untuk karyawan ini belum ditentukan atau format tidak valid!"
            ]);
        }

        // Tolak jika waktu scan di luar jam shift
        if (!$isAllowed) {
            return response()->json([
                'success' => false,
                'message' => "Gagal! Karyawan terdaftar di {$karyawan->shift}. Jadwal scan shift Anda adalah pukul {$shiftScheduleInfo} (Waktu saat ini: {$currentTime})."
            ]);
        }

        // =========================================================================
        // 3. VALIDASI TANGGAL BOOKING KARYAWAN (PENETAPAN KETAT)
        // =========================================================================
        $tanggalBookingKaryawan = Carbon::parse($karyawan->created_at)->toDateString();

        if ($tanggalBookingKaryawan !== $targetTanggalCatering) {
            $formattedBooking = Carbon::parse($tanggalBookingKaryawan)->format('d/m/Y');
            return response()->json([
                'success' => false,
                'message' => "Gagal! Anda tidak terdaftar untuk catering hari ini. Jadwal booking Anda adalah tanggal {$formattedBooking}."
            ]);
        }

        // 4. Cek apakah sudah mengambil porsi catering pada tanggal target tersebut
        $alreadyScanned = PengambilanCatering::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $targetTanggalCatering)
            ->exists();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => "Karyawan ({$karyawan->nama}) SUDAH mengambil catering untuk porsi tanggal {$targetTanggalCatering}!"
            ]);
        }

        // 5. Catat Pengambilan Catering ke Tanggal Target yang Tepat
        PengambilanCatering::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => $targetTanggalCatering,
            'waktu_scan'  => $now->format('H:i:s')
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Berhasil! Silakan ambil porsi catering.',
            'karyawan' => $karyawan
        ]);
    }

    // Halaman Booking Catering
    public function bookingIndex()
    {
        $today = Carbon::today()->toDateString();

        $sudahScanIds = PengambilanCatering::whereDate('tanggal', $today)
            ->pluck('karyawan_id')
            ->toArray();

        $karyawans = Karyawan::all()->map(function ($karyawan) use ($sudahScanIds) {
            $karyawan->status_catering = in_array($karyawan->id, $sudahScanIds) ? 'Sudah' : 'Belum';
            return $karyawan;
        });

        return view('booking', compact('karyawans'));
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

    // Update Data Karyawan & Status Catering Presisi
public function updateKaryawan(Request $request, $id)
{
    $karyawan = Karyawan::findOrFail($id);

    // 1. Ambil Field Utama Karyawan
    $updateData = $request->only(['nif', 'nama', 'departemen', 'lokasi', 'shift', 'no_hp']);

    // 2. Tangkap Input Tanggal Modal "tanggal" Dan Masukkan Ke Field "created_at"
    if ($request->filled('tanggal')) {
        $updateData['created_at'] = Carbon::parse($request->input('tanggal'))->format('Y-m-d H:i:s');
    } elseif ($request->filled('created_at')) {
        $updateData['created_at'] = Carbon::parse($request->input('created_at'))->format('Y-m-d H:i:s');
    }

    // Perbarui Data Karyawan di Database
    $karyawan->update($updateData);

    // 3. Synchronize Status Pengambilan Catering
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

    // Halaman WhatsApp QR / Catering Card
    public function whatsappIndex()
    {
        $karyawans = Karyawan::whereNotNull('no_hp')->get();
        return view('whatsapp', compact('karyawans'));
    }

    // Halaman Laporan Catering
    public function laporanIndex(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $filterStatus = $request->input('status', 'semua');

        $sudahScanIds = PengambilanCatering::whereBetween('tanggal', [$startDate, $endDate])
            ->pluck('karyawan_id')
            ->toArray();

        $query = Karyawan::query();

        $karyawans = $query->get()->map(function ($karyawan) use ($sudahScanIds, $startDate, $endDate) {
            $pengambilan = PengambilanCatering::where('karyawan_id', $karyawan->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->first();

            $karyawan->status_catering = in_array($karyawan->id, $sudahScanIds) ? 'Sudah' : 'Belum';
            $karyawan->waktu_scan = $pengambilan ? $pengambilan->waktu_scan : '-';
            $karyawan->tanggal_scan = $pengambilan ? $pengambilan->tanggal : '-';

            return $karyawan;
        });

        if ($filterStatus == 'sudah') {
            $karyawans = $karyawans->where('status_catering', 'Sudah');
        } elseif ($filterStatus == 'belum') {
            $karyawans = $karyawans->where('status_catering', 'Belum');
        }

        $totalKaryawan = Karyawan::count();
        $totalSudah = count($sudahScanIds);
        $totalBelum = max(0, $totalKaryawan - $totalSudah);

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
        $query = Karyawan::query();

        if ($request->filled('keyword')) {
            $keyword = trim($request->input('keyword'));
            $query->where('nif', $keyword);
        }

        $karyawans = $query->get();

        return view('cardcatering', compact('karyawans'));
    }
}