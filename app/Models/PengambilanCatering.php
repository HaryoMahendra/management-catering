<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanCatering extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan kolom diisi secara otomatis
    protected $fillable = [
        'karyawan_id', 
        'tanggal', 
        'waktu_scan'
    ];

    // Relasi ke Model Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}