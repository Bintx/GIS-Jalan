<?php

namespace App\Http\Controllers;

use App\Models\Jalan;
use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class JalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Untuk akses hanya Admin, sudah di route middleware
        $jalans = Jalan::with('regional')->latest()->paginate(10);
        return view('jalan.index', compact('jalans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionals = Regional::all();
        return view('jalan.create', compact('regionals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jalan' => 'required|string|max:255',
            'panjang_jalan' => 'required|numeric|min:0',
            'kondisi_jalan' => ['required', 'string', Rule::in(['baik', 'rusak ringan', 'rusak sedang', 'rusak berat'])],
            'regional_id' => 'required|exists:regional,id',
            'geometri_coords' => 'required|json',
        ]);

        $coordsFromFrontend = json_decode($validated['geometri_coords'], true);
        $geojsonCoordinates = array_map(function ($coord) {
            return [$coord[1], $coord[0]];
        }, $coordsFromFrontend);

        $geojsonLineString = [
            'type' => 'LineString',
            'coordinates' => $geojsonCoordinates
        ];

        Jalan::create([
            'nama_jalan' => $validated['nama_jalan'],
            'panjang_jalan' => $validated['panjang_jalan'],
            'kondisi_jalan' => $validated['kondisi_jalan'],
            'regional_id' => $validated['regional_id'],
            'geometri_json' => $geojsonLineString,
        ]);

        // Mengganti with('success', ...) menjadi with('success', ...) untuk SweetAlert2
        return redirect()->route('jalan.index')->with('success', 'Data Jalan ' . $validated['nama_jalan'] . ' berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jalan $jalan)
    {
        return view('jalan.show', compact('jalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jalan $jalan)
    {
        $regionals = Regional::all();
        $existingGeomCoords = '[]';
        if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates'])) {
            $lonLatCoords = $jalan->geometri_json['coordinates'];
            $mappedCoords = array_map(function ($coord) {
                return [$coord[1], $coord[0]];
            }, $lonLatCoords);
            $existingGeomCoords = json_encode($mappedCoords);
        }

        return view('jalan.edit', compact('jalan', 'regionals', 'existingGeomCoords'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jalan $jalan)
    {
        $validated = $request->validate([
            'nama_jalan' => 'required|string|max:255',
            'panjang_jalan' => 'required|numeric|min:0',
            'kondisi_jalan' => ['required', 'string', Rule::in(['baik', 'rusak ringan', 'rusak sedang', 'rusak berat'])],
            'regional_id' => 'required|exists:regional,id',
            'geometri_coords' => 'required|json',
        ]);

        $coordsFromFrontend = json_decode($validated['geometri_coords'], true);
        $geojsonCoordinates = array_map(function ($coord) {
            return [$coord[1], $coord[0]];
        }, $coordsFromFrontend);

        $geojsonLineString = [
            'type' => 'LineString',
            'coordinates' => $geojsonCoordinates
        ];

        $jalan->update([
            'nama_jalan' => $validated['nama_jalan'],
            'panjang_jalan' => $validated['panjang_jalan'],
            'kondisi_jalan' => $validated['kondisi_jalan'],
            'regional_id' => $validated['regional_id'],
            'geometri_json' => $geojsonLineString,
        ]);

        // Mengganti with('success', ...) menjadi with('success', ...) untuk SweetAlert2
        return redirect()->route('jalan.index')->with('success', 'Data Jalan ' . $jalan->nama_jalan . ' berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jalan $jalan)
    {
        $jalan->delete();
        // Mengganti with('success', ...) menjadi with('success', ...) untuk SweetAlert2
        return redirect()->route('jalan.index')->with('success', 'Data Jalan ' . $jalan->nama_jalan . ' berhasil dihapus!');
    }

    /**
     * Get road data by ID for AJAX request.
     * Includes regional data for context.
     */
    public function getJalanData(Jalan $jalan)
    {
        $jalan->load('regional');

        $tingkatKerusakanMap = [
            'baik' => '',
            'rusak ringan' => 'ringan',
            'rusak sedang' => 'sedang',
            'rusak berat' => 'berat',
        ];

        return response()->json([
            'id' => $jalan->id,
            'nama_jalan' => $jalan->nama_jalan,
            'panjang_jalan' => $jalan->panjang_jalan,
            'kondisi_jalan_master' => $jalan->kondisi_jalan,
            'regional_nama' => $jalan->regional->nama_regional ?? 'N/A',
            'regional_tipe' => $jalan->regional->tipe_regional ?? 'N/A',
            'suggested_tingkat_kerusakan' => $tingkatKerusakanMap[$jalan->kondisi_jalan] ?? '',
            'suggested_panjang_ruas_rusak' => $jalan->panjang_jalan,
        ]);
    }
}
