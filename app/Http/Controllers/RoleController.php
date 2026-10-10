<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = [
            ['id' => 1, 'nama' => 'Super Admin', 'slug' => 'super_admin', 'deskripsi' => 'Akses kelola seluruh menu'],
            ['id' => 2, 'nama' => 'Admin Catering', 'slug' => 'admin_catering', 'deskripsi' => 'Akses kelola menu scan, menu data karyawan dan menu laporan sesuai dengan lokasi'],
            ['id' => 3, 'nama' => 'Petugas Catering', 'slug' => 'petugas_catering', 'deskripsi' => 'Akses kelola menu scan sesuai dengan lokasi'],
        ];

        return view('user.role', compact('roles'));
    }
}