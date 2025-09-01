<?php

namespace App\Http\Controllers;

use App\Models\Jalan;
use App\Models\Regional;
use Illuminate\Http\Request; // Import Request
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class JalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Terima Request untuk filter
    {
        // Ambil filter dari request
        $filterNamaJalan = $request->query('nama_jalan');
        $filterKondisiJalan = $request->query('kondisi_jalan');
        $filterRegionalId = $request->query('regional_id'); // Ini bisa jadi ID RT, RW, atau Dusun

        // Query Jalan
        $queryJalan = Jalan::with(['regional', 'rwRegional', 'dusunRegional']);

        // Filter berdasarkan Nama Jalan
        if ($filterNamaJalan) {
            $queryJalan->where('nama_jalan', 'like', '%' . $filterNamaJalan . '%');
        }

        // Filter berdasarkan Kondisi Awal Jalan
        if ($filterKondisiJalan) {
            $queryJalan->where('kondisi_jalan', $filterKondisiJalan);
        }

        // Filter berdasarkan Regional ID (RT, RW, atau Dusun)
        if ($filterRegionalId) {
            $queryJalan->where(function ($query) use ($filterRegionalId) {
                $query->where('regional_id', $filterRegionalId) // Cek RT
                    ->orWhere('rw_regional_id', $filterRegionalId) // Cek RW
                    ->orWhere('dusun_regional_id', $filterRegionalId); // Cek Dusun
            });
        }

        $jalans = $queryJalan->latest()->get(); // Tambahkan withQueryString()

        // Ambil semua regional untuk filter dropdown
        $allRegionalsForFilter = Regional::all();

        // Teruskan data ke view, termasuk filter yang aktif
        return view('jalan.index', compact(
            'jalans',
            'allRegionalsForFilter',
            'filterNamaJalan',
            'filterKondisiJalan',
            'filterRegionalId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allRegionals = Regional::all();
        $rtRegionals = $allRegionals->where('tipe_regional', 'RT');
        $rwRegionals = $allRegionals->where('tipe_regional', 'RW');
        $dusunRegionals = $allRegionals->where('tipe_regional', 'Dusun');

        return view('jalan.create', compact('rtRegionals', 'rwRegionals', 'dusunRegionals'));
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
            'geometri_coords' => 'required|json',

            'rt_regional_id' => 'required|exists:regional,id',
            'rw_regional_id' => 'required|exists:regional,id',
            'dusun_regional_id' => 'required|exists:regional,id',
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
            'regional_id' => $validated['rt_regional_id'],       // ID RT
            'rw_regional_id' => $validated['rw_regional_id'],   // ID RW
            'dusun_regional_id' => $validated['dusun_regional_id'], // ID Dusun
            'geometri_json' => $geojsonLineString,
        ]);

        return redirect()->route('jalan.index')->with('success', 'Data Jalan ' . $validated['nama_jalan'] . ' berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jalan $jalan)
    {
        $jalan->load(['regional', 'rwRegional', 'dusunRegional']);
        return view('jalan.show', compact('jalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jalan $jalan)
    {
        $allRegionals = Regional::all();
        $rtRegionals = $allRegionals->where('tipe_regional', 'RT');
        $rwRegionals = $allRegionals->where('tipe_regional', 'RW');
        $dusunRegionals = $allRegionals->where('tipe_regional', 'Dusun');

        // Untuk edit: pastikan data lama terpilih di dropdown
        $selectedRtId = old('rt_regional_id', $jalan->regional_id);
        $selectedRwId = old('rw_regional_id', $jalan->rw_regional_id);
        $selectedDusunId = old('dusun_regional_id', $jalan->dusun_regional_id);

        $existingGeomCoords = '[]';
        if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates'])) {
            $lonLatCoords = $jalan->geometri_json['coordinates'];
            $mappedCoords = array_map(function ($coord) {
                return [$coord[1], $coord[0]];
            }, $lonLatCoords);
            $existingGeomCoords = json_encode($mappedCoords);
        }

        return view('jalan.edit', compact(
            'jalan',
            'rtRegionals',
            'rwRegionals',
            'dusunRegionals',
            'selectedRtId',
            'selectedRwId',
            'selectedDusunId',
            'existingGeomCoords'
        ));
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
            'geometri_coords' => 'required|json',

            'rt_regional_id' => 'required|exists:regional,id',
            'rw_regional_id' => 'required|exists:regional,id',
            'dusun_regional_id' => 'required|exists:regional,id',
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
            'regional_id' => $validated['rt_regional_id'],
            'rw_regional_id' => $validated['rw_regional_id'],
            'dusun_regional_id' => $validated['dusun_regional_id'],
            'geometri_json' => $geojsonLineString,
        ]);

        return redirect()->route('jalan.index')->with('success', 'Data Jalan ' . $jalan->nama_jalan . ' berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jalan $jalan)
    {
        $jalan->delete();
        return redirect()->route('jalan.index')->with('success', 'Data Jalan ' . $jalan->nama_jalan . ' berhasil dihapus!');
    }

    /**
     * Get road data by ID for AJAX request.
     * Includes regional data for context.
     */
    public function getJalanData(Jalan $jalan)
    {
        $jalan->load(['regional', 'rwRegional', 'dusunRegional']); // Pastikan load relasi baru

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
            // Perbarui ini untuk menampilkan semua info regional
            'regional_rt_nama' => $jalan->regional->nama_regional ?? 'N/A',
            'regional_rw_nama' => $jalan->rwRegional->nama_regional ?? 'N/A',
            'regional_dusun_nama' => $jalan->dusunRegional->nama_regional ?? 'N/A',
            'suggested_tingkat_kerusakan' => $tingkatKerusakanMap[$jalan->kondisi_jalan] ?? '',
            'suggested_panjang_ruas_rusak' => $jalan->panjang_jalan,
        ]);
    }
}
