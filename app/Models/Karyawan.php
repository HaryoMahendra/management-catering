<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_no',      
        'name',             
        'employment_under', 
        'departemen',       
        'indonesia_level',  
        'location',         
        'schedule',         
        'no_hp',
        'created_at',       
        'updated_at'
    ];

    protected static function booted()
    {
        static::observe(\App\Observers\ActivityObserver::class);
    }

    public function pengambilanCatering()
    {
        return $this->hasMany(PengambilanCatering::class);
    }
}