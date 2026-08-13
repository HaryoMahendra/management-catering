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

    // Process Scan QR
    public function processScan(Request $request)
    {
        $nif = $request->input('nif');
        $today = Carbon::today()->toDateString();

        $karyawan = Karyawan::where('nif', $nif)->first();
        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'NIF / QR Code tidak terdaftar!']);
        }

        $alreadyScanned = PengambilanCatering::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan (' . $karyawan->nama . ') SUDAH mengambil catering hari ini!'
            ]);
        }

        PengambilanCatering::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => $today,
            'waktu_scan'  => Carbon::now()->format('H:i:s')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil! Silakan ambil porsi catering.',
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

        // Update Field Utama
        $updateData = $request->only(['nif', 'nama', 'departemen', 'lokasi', 'shift', 'no_hp']);

        if ($request->has('created_at') && $request->created_at != null) {
            $updateData['created_at'] = Carbon::parse($request->created_at)->toDateTimeString();
        }

        $karyawan->update($updateData);

        // Update Status Catering Sesuai Tanggal Form Edit
        if ($request->has('status_catering')) {
            // Tangkap tanggal pilihan dari input form edit (fallback ke hari ini jika kosong)
            $targetTanggal = $request->filled('tanggal')
                ? Carbon::parse($request->input('tanggal'))->toDateString()
                : Carbon::today()->toDateString();

            $status = $request->input('status_catering');

            if ($status == 'sudah') {
                PengambilanCatering::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'tanggal'     => $targetTanggal,
                    ],
                    [
                        'waktu_scan'  => Carbon::now()->format('H:i:s')
                    ]
                );
            } else {
                PengambilanCatering::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $targetTanggal)
                    ->delete();
            }
        }

        return redirect()->back()->with('success', 'Data karyawan dan status catering berhasil diperbarui!');
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

    // Halaman Card Catering (Mendukung Filter Spesifik via Parameter NIF / Keyword)
    public function cardCatering(Request $request)
    {
        $query = Karyawan::query();

        // Jika ada parameter keyword/nif di URL (misal: /cardcatering?keyword=NIF-001)
        if ($request->filled('keyword')) {
            $keyword = trim($request->input('keyword'));
            $query->where('nif', $keyword);
        }

        $karyawans = $query->get();

        return view('cardcatering', compact('karyawans'));
    }
}