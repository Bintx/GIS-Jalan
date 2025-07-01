<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan melihat daftar user.');
        }

        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan menambah user.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'pejabat_desa'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'User ' . $validated['name'] . ' berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan melihat detail user.');
        }
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan mengedit user.');
        }
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan memperbarui user.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'pejabat_desa'])],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        if (Auth::id() === $user->id && $validated['role'] !== 'admin' && User::where('role', 'admin')->count() === 1) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat mengubah peran Anda sendiri karena Anda adalah admin terakhir.');
        }

        $user->update($dataToUpdate);

        return redirect()->route('users.index')->with('success', 'User ' . $user->name . ' berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan menghapus user.');
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus admin terakhir.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User ' . $user->name . ' berhasil dihapus!');
    }
}
