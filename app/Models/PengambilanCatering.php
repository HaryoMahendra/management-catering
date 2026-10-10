<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanCatering extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id', 
        'tanggal', 
        'waktu_scan'
    ];

    protected static function booted()
    {
        static::observe(\App\Observers\ActivityObserver::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}