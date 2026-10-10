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
        // 1. Ambil nilai Employee No
        $employeeNo = trim($row['employee_no'] ?? $row['nik'] ?? '');
        if (empty($employeeNo)) {
            return null;
        }

        // 2. Olah Format Tanggal dari Excel
        $tanggalBooking = now();
        $rawTanggal = $row['tanggal'] ?? $row['date'] ?? null;

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

        // 3. Selalu instansiasi data baru (menghapus pencarian berdasarkan employee_no agar data kembar/berbeda shift tetap tersimpan sebagai baris baru)
        $karyawan = new Karyawan();

        // 4. Isi Atribut Data Utama (Konsisten menggunakan 'location')
        $karyawan->employee_no      = $employeeNo;
        $karyawan->name             = $row['name'] ?? $row['nama'] ?? $row['nama_karyawan'] ?? '-';
        $karyawan->employment_under = $row['employment_under'] ?? null;
        $karyawan->departemen       = $row['dept'] ?? $row['departemen'] ?? null;
        $karyawan->indonesia_level  = $row['indonesia_level'] ?? null;
        $karyawan->location         = $row['location'] ?? $row['lokasi'] ?? null;
        $karyawan->schedule         = $row['schedule'] ?? $row['shift'] ?? null;
        $karyawan->no_hp            = $row['no_hp'] ?? $row['nohp'] ?? null;

        // Paksa timpa created_at dengan tanggal dari Excel
        $karyawan->created_at = $tanggalBooking->format('Y-m-d H:i:s');

        // 5. Simpan Data ke Database
        $karyawan->save();

        return $karyawan;
    }
}