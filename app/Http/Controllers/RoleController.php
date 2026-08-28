<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = [
            ['id' => 1, 'nama' => 'Super Admin', 'slug' => 'super_admin', 'deskripsi' => 'Akses penuh ke seluruh sistem'],
            ['id' => 2, 'nama' => 'Admin Catering', 'slug' => 'admin_catering', 'deskripsi' => 'Akses kelola booking dan laporan'],
            ['id' => 3, 'nama' => 'Petugas Catering', 'slug' => 'petugas_catering', 'deskripsi' => 'Akses khusus scan QR Code dan Laporan'],
        ];

        return view('user.role', compact('roles'));
    }
}