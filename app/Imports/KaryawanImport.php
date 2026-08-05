<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KaryawanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Menggunakan updateOrCreate berdasarkan NIF agar tidak ada data ganda
        return Karyawan::updateOrCreate(
            ['nif' => $row['nif'] ?? $row['nik']], // Membaca kolom 'nif' atau 'nik' dari Excel
            [
                'nama'       => $row['nama'] ?? $row['nama_karyawan'],
                'departemen' => $row['departemen'] ?? null,
                'lokasi'     => $row['lokasi'] ?? null,
                'shift'      => $row['shift'] ?? null,
                'no_hp'      => $row['no_hp'] ?? $row['nohp'] ?? null,
            ]
        );
    }
}