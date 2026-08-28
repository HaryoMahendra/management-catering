<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $hiddenEmails = [
        'masteradmin@gmail.com',
    ];

    public function index()
    {
        $users = User::whereNotIn('email', $this->hiddenEmails)->get();
        return view('user.user', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:super_admin,admin_catering,petugas_catering'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->back()->with('success', 'Data User berhasil ditambahkan!');
    }

    // Fungsi Update User
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (in_array($user->email, $this->hiddenEmails)) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|in:super_admin,admin_catering,petugas_catering'
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Data User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (in_array($user->email, $this->hiddenEmails)) {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Data User berhasil dihapus!');
    }
}