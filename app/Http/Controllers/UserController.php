<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class UserController extends Controller
{
    private $hiddenEmails = [
        'masteradmin@gmail.com',
    ];

    public function index()
    {
        $users = User::whereNotIn('email', $this->hiddenEmails)->get();
        
        $activityLogs = ActivityLog::with('user')->latest()->get();

        return view('user.user', compact('users', 'activityLogs'));
    }

    public function store(Request $request)
    {
        $lokasi = $request->input('lokasi');
        $departemen = $request->input('departemen');

        if (is_string($lokasi)) {
            $lokasi = [$lokasi];
        }

        if (is_string($departemen)) {
            $departemen = [$departemen];
        }

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'role'       => 'required|in:super_admin,admin_catering,petugas_catering',
            'lokasi'     => 'nullable|array',
            'departemen' => 'nullable|array',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'lokasi'     => $lokasi ?: null,
            'departemen' => $departemen ?: null,
        ]);

        return redirect()->back()->with('success', 'Data User berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->email === 'mastergcpi26@godrej.com' && Auth::user()->email !== 'mastergcpi26@godrej.com') {
            return redirect()->back()->with('error', 'Akses ditolak! Akun Master hanya dapat diubah oleh Master User itu sendiri.');
        }

        if (in_array($user->email, $this->hiddenEmails)) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $lokasi = $request->input('lokasi');
        $departemen = $request->input('departemen');

        if (is_string($lokasi)) {
            $lokasi = [$lokasi];
        }

        if (is_string($departemen)) {
            $departemen = [$departemen];
        }

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'role'       => 'required|in:super_admin,admin_catering,petugas_catering',
            'lokasi'     => 'nullable|array',
            'departemen' => 'nullable|array',
        ]);

        $user->name       = $request->name;
        $user->email      = $request->email;
        $user->role       = $request->role;
        $user->lokasi     = $lokasi ?: null;
        $user->departemen = $departemen ?: null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Data User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->email === 'mastergcpi26@godrej.com' && Auth::user()->email !== 'mastergcpi26@godrej.com') {
            return redirect()->back()->with('error', 'Akses ditolak! Akun Master tidak dapat dihapus oleh Super Admin lain.');
        }

        if (in_array($user->email, $this->hiddenEmails)) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Data User berhasil dihapus!');
    }
}