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
        $filterJenisJalan = $request->query('jenis_jalan');
        $filterRegionalId = $request->query('regional_id'); // Ini bisa jadi ID RT, RW, atau Dusun

        // Query Jalan
        $queryJalan = Jalan::with(['regional', 'rwRegional', 'dusunRegional']);

        // Filter berdasarkan Nama Jalan
        if ($filterNamaJalan) {
            $queryJalan->where('nama_jalan', 'like', '%' . $filterNamaJalan . '%');
        }

        // Filter berdasarkan Kondisi Awal Jalan
        if ($filterJenisJalan) {
            $queryJalan->where('jenis_jalan', $filterJenisJalan);
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
            'filterJenisJalan',
            'filterRegionalId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionals = Regional::all();
        return view('jalan.create', compact('regionals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jalan' => 'required|string|max:255|unique:jalan,nama_jalan',
            'panjang_jalan' => 'required|numeric|min:0',
            'jenis_jalan' => ['required', 'string', Rule::in(['aspal', 'beton', 'paving', 'tanah'])],
            'regional_id' => 'required|exists:regional,id',
            'rw_regional_id' => 'required|exists:regional,id',
            'dusun_regional_id' => 'required|exists:regional,id',
            'geometri_json' => 'required|json',
        ]);

        Jalan::create($validated);
        return redirect()->route('jalan.index')->with('success', 'Data jalan berhasil ditambahkan!');
    }

    public function show(Jalan $jalan)
    {
        $existingGeomCoords = '[]';
        if ($jalan->geometri_json) {
            $geo = json_decode($jalan->geometri_json, true);
            if (isset($geo['coordinates'])) {
                // GeoJSON format [lng, lat] → Leaflet [lat, lng]
                $coords = array_map(fn($c) => [$c[1], $c[0]], $geo['coordinates']);
                $existingGeomCoords = json_encode($coords);
            }
        }
        return view('jalan.show', compact('jalan', 'existingGeomCoords'));
    }

    public function edit(Jalan $jalan)
    {
        $regionals = Regional::all();

        // Ambil koordinat dari geometri_json jika ada
        $existingGeomCoords = '[]';
        if ($jalan->geometri_json) {
            $geo = json_decode($jalan->geometri_json, true);
            if (isset($geo['coordinates'])) {
                // GeoJSON format [lng, lat] → Leaflet [lat, lng]
                $coords = array_map(fn($c) => [$c[1], $c[0]], $geo['coordinates']);
                $existingGeomCoords = json_encode($coords);
            }
        }

        return view('jalan.edit', compact('jalan', 'regionals', 'existingGeomCoords'));
    }

    public function update(Request $request, Jalan $jalan)
    {
        $validated = $request->validate([
            'nama_jalan' => 'required|string|max:255|unique:jalan,nama_jalan,' . $jalan->id,
            'panjang_jalan' => 'required|numeric|min:0',
            'jenis_jalan' => ['required', 'string', Rule::in(['aspal', 'beton', 'paving', 'tanah'])],
            'regional_id' => 'required|exists:regional,id',
            'rw_regional_id' => 'required|exists:regional,id',
            'dusun_regional_id' => 'required|exists:regional,id',
            'geometri_json' => 'required|json',
        ]);

        $jalan->update($validated);
        return redirect()->route('jalan.index')->with('success', 'Data jalan berhasil diperbarui!');
    }

    public function destroy(Jalan $jalan)
    {
        try {
            $jalan->delete();
            return redirect()->route('jalan.index')->with('success', 'Data jalan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('jalan.index')->with('error', 'Gagal menghapus data jalan. Mungkin masih terkait dengan laporan kerusakan.');
        }
    }

    public function getAllJalanGeometries()
    {
        $jalans = Jalan::with(['regional', 'rwRegional', 'dusunRegional', 'kerusakanJalan'])->get();
        return response()->json($jalans);
    }
    public function getJalanData(Jalan $jalan)
    {
        // Muat relasi yang dibutuhkan untuk jalan yang spesifik
        $jalan->load(['regional', 'rwRegional', 'dusunRegional']);

        // Kembalikan data dalam format JSON yang diharapkan oleh JavaScript
        return response()->json([
            'id' => $jalan->id,
            'jenis_jalan' => $jalan->jenis_jalan,
            'regional_rt_nama' => $jalan->regional->nama_regional ?? 'N/A',
            'regional_rw_nama' => $jalan->rwRegional->nama_regional ?? 'N/A',
            'regional_dusun_nama' => $jalan->dusunRegional->nama_regional ?? 'N/A',
            'suggested_panjang_ruas_rusak' => $jalan->panjang_jalan,
        ]);
    }
}
