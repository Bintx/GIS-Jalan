<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // Untuk hashing password jika ganti password
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang login

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hanya Admin yang bisa melihat daftar user
        // Middleware 'admin' sudah melindungi rute ini, tapi pengecekan di controller juga baik.
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Dilarang. Hanya Admin yang diizinkan.');
        }

        $users = User::latest()->paginate(10); // Ambil semua user, paginasi 10 per halaman
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     * Umumnya, pendaftaran user baru ditangani oleh fitur register Breeze,
     * tapi jika Admin butuh form khusus, ini bisa diimplementasikan.
     * Untuk saat ini, kita akan biarkan sederhana atau arahkan ke register jika perlu.
     */
    public function create()
    {
        // Bisa redirect ke halaman register atau buat form custom untuk admin
        // return redirect()->route('register');
        return view('users.create'); // Kita akan buat view ini untuk Admin
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Dilarang. Hanya Admin yang diizinkan untuk menambah user.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' butuh password_confirmation
            'role' => ['required', 'string', Rule::in(['admin', 'pejabat_desa'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(), // Verifikasi otomatis jika ditambah admin
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Admin bisa melihat detail user lain
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Dilarang. Hanya Admin yang diizinkan melihat detail user.');
        }
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Admin bisa edit user lain, tapi tidak boleh edit dirinya sendiri di sini jika rolenya mau diganti
        // atau jika rolenya mau diganti ke non-admin dan dia adalah admin terakhir.
        // Untuk sederhana, Admin bisa edit semua kecuali rolenya sendiri.
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Dilarang. Hanya Admin yang diizinkan mengedit user.');
        }
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Dilarang. Hanya Admin yang diizinkan memperbarui user.');
        }

        // Aturan validasi
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'pejabat_desa'])],
        ];

        // Tambahkan validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        // Pencegahan agar admin tidak menghapus/menurunkan dirinya sendiri menjadi non-admin
        // jika dia adalah admin terakhir di sistem.
        if (Auth::id() === $user->id && $validated['role'] !== 'admin' && User::where('role', 'admin')->count() === 1) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat mengubah peran Anda sendiri karena Anda adalah admin terakhir.');
        }


        $user->update($dataToUpdate);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Dilarang. Hanya Admin yang diizinkan menghapus user.');
        }

        // Pencegahan agar Admin tidak menghapus dirinya sendiri
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Pencegahan agar Admin tidak menghapus Admin terakhir
        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus admin terakhir.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
