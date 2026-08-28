<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class KaryawanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Ambil nilai NIK/NIF
        $nif = trim($row['nik'] ?? $row['nif'] ?? '');
        if (empty($nif)) {
            return null;
        }

        // 2. Olah Format Tanggal dari Excel
        $tanggalBooking = now();
        $rawTanggal = $row['tanggal'] ?? null;

        if (!empty($rawTanggal)) {
            try {
                if (is_numeric($rawTanggal)) {
                    // Jika format di Excel berupa Serial Date Number
                    $tanggalBooking = Carbon::instance(ExcelDate::excelToDateTimeObject($rawTanggal));
                } else {
                    // Jika format di Excel berupa string teks (misal: "20/08/2026")
                    $tanggalBooking = Carbon::createFromFormat('d/m/Y', trim($rawTanggal));
                }
            } catch (\Exception $e) {
                try {
                    $tanggalBooking = Carbon::parse($rawTanggal);
                } catch (\Exception $ex) {
                    $tanggalBooking = now();
                }
            }
        }

        // 3. Cari atau Buat Karyawan Baru
        $karyawan = Karyawan::firstOrNew(['nif' => $nif]);

        // 4. Isi Atribut Data
        $karyawan->nama       = $row['nama'] ?? $row['nama_karyawan'] ?? '-';
        $karyawan->departemen = $row['departemen'] ?? null;
        $karyawan->lokasi     = $row['lokasi'] ?? null;
        $karyawan->shift      = $row['shift'] ?? null;
        $karyawan->no_hp      = $row['no_hp'] ?? $row['nohp'] ?? null;
        
        // Paksa timpa created_at dengan tanggal dari Excel
        $karyawan->created_at = $tanggalBooking->format('Y-m-d H:i:s');

        // 5. Simpan (Memaksa simpan created_at baik data baru maupun update)
        $karyawan->save();

        return $karyawan;
    }
}