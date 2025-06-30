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
    public function index(Request $request)
    {
        // $filterNamaRegional = $request->query('nama_regional');
        $filterTipeRegional = $request->query('tipe_regional');

        $queryRegional = Regional::query();

        // if ($filterNamaRegional) {
        //     $queryRegional->where('nama_regional', 'like', '%' . $filterNamaRegional . '%');
        // }

        if ($filterTipeRegional) {
            $queryRegional->where('tipe_regional', $filterTipeRegional);
        }

        // Ambil SEMUA data yang sudah difilter untuk paginasi sisi klien oleh DataTables
        $regionals = $queryRegional->latest()->get(); // <-- PERUBAHAN DI SINI: .get() bukan .paginate()

        return view('regional.index', compact(
            'regionals',
            // 'filterNamaRegional',
            'filterTipeRegional'
        ));
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
            'tipe_regional' => ['required', 'string', Rule::in(['RT', 'RW', 'Dusun'])],
        ]);

        Regional::create($validated);

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
            'tipe_regional' => ['required', 'string', Rule::in(['RT', 'RW', 'Dusun'])],
        ]);

        $regional->update($validated);

        return redirect()->route('regional.index')->with('success', 'Data Regional ' . $regional->nama_regional . ' berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regional $regional)
    {
        $regional->delete();
        return redirect()->route('regional.index')->with('success', 'Data Regional ' . $regional->nama_regional . ' berhasil dihapus!');
    }
}
