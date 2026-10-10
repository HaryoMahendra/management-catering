<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->email !== 'mastergcpi26@godrej.com') {
                abort(403, 'Unauthorized action. Halaman ini khusus untuk Master Admin.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::all();
        
        $activityLogs = ActivityLog::with('user')->latest()->get();

        $totalSuperAdmin = User::where('role', 'super_admin')->count();
        $totalAdminCatering = User::where('role', 'admin_catering')->count();
        $totalPetugas = User::where('role', 'petugas_catering')->count();

        return view('user.user_master', compact('users', 'totalSuperAdmin', 'totalAdminCatering', 'totalPetugas', 'activityLogs'));
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

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'lokasi'     => $lokasi ?: null,
            'departemen' => $departemen ?: null,
        ]);

        return redirect()->route('user.master.index')->with('success', 'User master berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

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

        return redirect()->route('user.master.index')->with('success', 'Data user master berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan!');
        }
        
        $user->delete();

        return redirect()->route('user.master.index')->with('success', 'User master berhasil dihapus!');
    }
}