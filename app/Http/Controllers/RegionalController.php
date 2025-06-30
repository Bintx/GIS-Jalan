<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Untuk akses hanya Admin, sudah di route middleware
        $regionals = Regional::latest()->paginate(10);
        return view('regional.index', compact('regionals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('regional.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_regional' => 'required|string|max:255|unique:regional,nama_regional',
            'tipe_regional' => ['required', 'string', Rule::in(['RT', 'RW', 'Desa', 'Kecamatan'])],
        ]);

        Regional::create($validated);

        // Mengganti with('success', ...) menjadi with('success', ...) untuk SweetAlert2
        return redirect()->route('regional.index')->with('success', 'Data Regional ' . $validated['nama_regional'] . ' berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Regional $regional)
    {
        return view('regional.show', compact('regional'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Regional $regional)
    {
        return view('regional.edit', compact('regional'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Regional $regional)
    {
        $validated = $request->validate([
            'nama_regional' => 'required|string|max:255|unique:regional,nama_regional,' . $regional->id,
            'tipe_regional' => ['required', 'string', Rule::in(['RT', 'RW', 'Desa', 'Kecamatan'])],
        ]);

        $regional->update($validated);

        // Mengganti with('success', ...) menjadi with('success', ...) untuk SweetAlert2
        return redirect()->route('regional.index')->with('success', 'Data Regional ' . $regional->nama_regional . ' berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regional $regional)
    {
        $regional->delete();
        // Mengganti with('success', ...) menjadi with('success', ...) untuk SweetAlert2
        return redirect()->route('regional.index')->with('success', 'Data Regional ' . $regional->nama_regional . ' berhasil dihapus!');
    }
}
