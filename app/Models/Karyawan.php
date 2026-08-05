<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = ['nif', 'nama', 'departemen', 'lokasi', 'shift', 'no_hp'];

    public function pengambilanCatering()
    {
        return $this->hasMany(PengambilanCatering::class);
    }
}