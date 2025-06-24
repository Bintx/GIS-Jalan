<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regionals = Regional::latest()->paginate(10); // Ambil semua data regional, paginasi 10 per halaman
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

        return redirect()->route('regional.index')->with('success', 'Data Regional berhasil ditambahkan!');
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
            'nama_regional' => 'required|string|max:255|unique:regional,nama_regional,' . $regional->id, // Unique kecuali ID ini
            'tipe_regional' => ['required', 'string', Rule::in(['RT', 'RW', 'Desa', 'Kecamatan'])],
        ]);

        $regional->update($validated);

        return redirect()->route('regional.index')->with('success', 'Data Regional berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regional $regional)
    {
        $regional->delete();

        return redirect()->route('regional.index')->with('success', 'Data Regional berhasil dihapus!');
    }
}
