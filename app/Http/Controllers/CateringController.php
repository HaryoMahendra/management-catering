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

        $totalKaryawan = Karyawan::count();
        $sudahMengambil = PengambilanCatering::whereDate('tanggal', $today)->count();
        $belumMengambil = max(0, $totalKaryawan - $sudahMengambil);
        $persentase     = $totalKaryawan > 0 ? round(($sudahMengambil / $totalKaryawan) * 100) : 0;

        $ringkasanMonitoring = PengambilanCatering::selectRaw('
                tanggal,
                COUNT(DISTINCT karyawan_id) as sudah_mengambil
            ')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) use ($totalKaryawan) {
                $item->total_karyawan = $totalKaryawan;
                $item->belum_mengambil = max(0, $totalKaryawan - $item->sudah_mengambil);
                $item->persentase = $totalKaryawan > 0 
                    ? round(($item->sudah_mengambil / $totalKaryawan) * 100, 1) 
                    : 0;

                return $item;
            });

        return view('dashboard', compact(
            'totalKaryawan', 
            'sudahMengambil', 
            'belumMengambil', 
            'persentase',
            'ringkasanMonitoring' // Ditambahkan ke compact
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

        $karyawans = Karyawan::all()->map(function($karyawan) use ($sudahScanIds) {
            $karyawan->status_catering = in_array($karyawan->id, $sudahScanIds) ? 'Sudah' : 'Belum';
            return $karyawan;
        });

        return view('booking', compact('karyawans'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new KaryawanImport, $request->file('file_excel'));

        return redirect()->back()->with('success', 'Data Karyawan berhasil di-import dari Excel!');
    }

    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Update Field Dasar Karyawan
        $updateData = $request->only(['nif', 'nama', 'departemen', 'lokasi', 'shift', 'no_hp']);

        // Update Tanggal (created_at) jika diisi
        if ($request->has('created_at') && $request->created_at != null) {
            $updateData['created_at'] = Carbon::parse($request->created_at)->toDateTimeString();
        }

        $karyawan->update($updateData);

        // Update Status Catering Hari Ini
        if ($request->has('status_catering')) {
            $today = Carbon::today()->toDateString();
            $status = $request->input('status_catering');

            if ($status == 'sudah') {
                // Jika diubah ke SUDAH, tambahkan record ke PengambilanCatering hari ini jika belum ada
                PengambilanCatering::firstOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'tanggal'     => $today,
                    ],
                    [
                        'waktu_scan'  => Carbon::now()->format('H:i:s')
                    ]
                );
            } else {
                // Jika diubah ke BELUM, hapus record PengambilanCatering hari ini
                PengambilanCatering::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $today)
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

    // Halaman WhatsApp Blast QR
    public function whatsappIndex()
    {
        $karyawans = Karyawan::whereNotNull('no_hp')->get();
        return view('whatsapp', compact('karyawans'));
    }

    // Proses Kirim WA Blast via API Saung WA
    public function whatsappSendBlast(Request $request)
    {
        $karyawanIds = $request->input('karyawan_ids', []);

        if (empty($karyawanIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu karyawan untuk dikirimi WhatsApp!');
        }

        // Endpoint & Config API SaungWA
        $targetUrl = env('SAUNG_WA_URL', 'https://app.saungwa.com/api/send-message');
        $apiKey    = env('SAUNG_WA_API_KEY', '3LLxtpwipbJJsotqlOu4Q5JO9sivKjKWozERUfOC4EjWsPN6UJ');

        $karyawans = Karyawan::whereIn('id', $karyawanIds)->get();
        $sukses = 0;
        $gagal = 0;

        foreach ($karyawans as $karyawan) {
            if (empty($karyawan->no_hp)) {
                $gagal++;
                continue;
            }

            // Normalisasi Format Nomor HP ke 62
            $targetHp = preg_replace('/[^0-9]/', '', $karyawan->no_hp);
            if (substr($targetHp, 0, 1) === '0') {
                $targetHp = '62' . substr($targetHp, 1);
            }

            $linkScan = route('scan.index', ['nif' => $karyawan->nif]);

            $pesan = "Halo *{$karyawan->nama}*,\n\n";
            $pesan .= "Silakan klik link berikut untuk membuka QR Code Pengambilan Catering Anda (NIF: *{$karyawan->nif}*):\n\n";
            $pesan .= "👉 {$linkScan}\n\n";
            $pesan .= "Tunjukkan QR Code di link tersebut kepada petugas catering.\n\n";
            $pesan .= "Terima kasih,\n_Management Catering_";

            try {
                // Request dengan format JSON & Header Authorization khas SaungWA
                $response = Http::acceptJson()->post($targetUrl, [
                    'api_key' => $apiKey,
                    'number'  => $targetHp,
                    'message' => $pesan,
                ]);

                // Verifikasi respon balasan dari SaungWA
                $responseData = $response->json();

                if ($response->successful() && isset($responseData['status']) && $responseData['status'] == true) {
                    $sukses++;
                } else {
                    Log::error("Gagal kirim WA ke {$targetHp}: " . json_encode($responseData));
                    $gagal++;
                }
            } catch (\Exception $e) {
                Log::error("Exception WA Blast: " . $e->getMessage());
                $gagal++;
            }
        }

        return redirect()->back()->with('success', "Proses Blast Selesai! Berhasil dikirim: {$sukses}, Gagal: {$gagal}");
    }

    // Halaman Laporan Catering
    public function laporanIndex(Request $request)
    {
        // Default rentang tanggal: Hari ini
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $filterStatus = $request->input('status', 'semua'); // semua / sudah / belum

        // Ambil ID karyawan yang SUDAH scan pada rentang tanggal tersebut
        $sudahScanIds = PengambilanCatering::whereBetween('tanggal', [$startDate, $endDate])
            ->pluck('karyawan_id')
            ->toArray();

        // Query Dasar Karyawan
        $query = Karyawan::query();

        // Ambil semua karyawan dan tentukan statusnya
        $karyawans = $query->get()->map(function($karyawan) use ($sudahScanIds, $startDate, $endDate) {
            // Cari detail pengambilan jika sudah pernah scan
            $pengambilan = PengambilanCatering::where('karyawan_id', $karyawan->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->first();

            $karyawan->status_catering = in_array($karyawan->id, $sudahScanIds) ? 'Sudah' : 'Belum';
            $karyawan->waktu_scan = $pengambilan ? $pengambilan->waktu_scan : '-';
            $karyawan->tanggal_scan = $pengambilan ? $pengambilan->tanggal : '-';
            
            return $karyawan;
        });

        // Filter berdasarkan status jika dipilih
        if ($filterStatus == 'sudah') {
            $karyawans = $karyawans->where('status_catering', 'Sudah');
        } elseif ($filterStatus == 'belum') {
            $karyawans = $karyawans->where('status_catering', 'Belum');
        }

        // Hitung Ringkasan Data
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

    // Test Barcode View
    public function testBarcode()
    {
        $karyawans = Karyawan::all();
        return view('test-barcode', compact('karyawans'));
    }
}