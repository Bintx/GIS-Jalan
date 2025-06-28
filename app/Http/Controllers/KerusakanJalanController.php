<?php

namespace App\Http\Controllers;

use App\Models\KerusakanJalan;
use App\Models\Jalan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\NaiveBayesClassifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KerusakanJalanExport;

class KerusakanJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Terima Request untuk filter
    {
        // Ambil filter dari request
        $filterNamaJalan = $request->query('nama_jalan');
        $filterTingkatKerusakan = $request->query('tingkat_kerusakan');
        $filterPrioritas = $request->query('prioritas');
        $filterStatusPerbaikan = $request->query('status_perbaikan');

        // Query Laporan Kerusakan Jalan
        $queryLaporan = KerusakanJalan::with(['jalan.regional', 'user']);

        // Filter berdasarkan Nama Jalan (gunakan pencarian like di relasi)
        if ($filterNamaJalan) {
            $queryLaporan->whereHas('jalan', function ($query) use ($filterNamaJalan) {
                $query->where('nama_jalan', 'like', '%' . $filterNamaJalan . '%');
            });
        }

        // Filter berdasarkan Tingkat Kerusakan
        if ($filterTingkatKerusakan) {
            $queryLaporan->where('tingkat_kerusakan', $filterTingkatKerusakan);
        }

        // Filter berdasarkan Prioritas Klasifikasi
        if ($filterPrioritas) {
            if ($filterPrioritas === 'belum_diklasifikasi') {
                $queryLaporan->whereNull('klasifikasi_prioritas');
            } else {
                $queryLaporan->where('klasifikasi_prioritas', $filterPrioritas);
            }
        }

        // Filter berdasarkan Status Perbaikan
        if ($filterStatusPerbaikan) {
            $queryLaporan->where('status_perbaikan', str_replace('_', ' ', $filterStatusPerbaikan));
        }

        // Ini adalah baris di mana withQueryString() digunakan
        $kerusakanJalans = $queryLaporan->latest()->paginate(10)->withQueryString();

        // Ambil semua nama jalan untuk filter dropdown/autocomplete
        $allJalanNames = Jalan::select('id', 'nama_jalan')->get();

        // Teruskan data ke view, termasuk filter yang aktif
        return view('kerusakan_jalan.index', compact(
            'kerusakanJalans',
            'allJalanNames', // Untuk filter nama jalan
            'filterNamaJalan',
            'filterTingkatKerusakan',
            'filterPrioritas',
            'filterStatusPerbaikan'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jalans = Jalan::all();
        return view('kerusakan_jalan.create', compact('jalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, NaiveBayesClassifier $classifier)
    {
        $validated = $request->validate([
            'jalan_id' => 'required|exists:jalan,id',
            'tanggal_lapor' => 'required|date',
            'tingkat_kerusakan' => ['required', 'string', Rule::in(['ringan', 'sedang', 'berat'])],
            'tingkat_lalu_lintas' => ['required', 'string', Rule::in(['rendah', 'sedang', 'tinggi'])],
            'panjang_ruas_rusak' => 'required|numeric|min:0',
            'deskripsi_kerusakan' => 'nullable|string|max:1000',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_kerusakan')) {
            $fotoPath = $request->file('foto_kerusakan')->store('kerusakan_jalan_photos', 'public');
        }

        $prioritasKlasifikasi = $classifier->classify(
            $validated['tingkat_kerusakan'],
            $validated['tingkat_lalu_lintas'],
            $validated['panjang_ruas_rusak']
        );

        KerusakanJalan::create([
            'jalan_id' => $validated['jalan_id'],
            'user_id' => Auth::id(),
            'tanggal_lapor' => $validated['tanggal_lapor'],
            'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
            'tingkat_lalu_lintas' => $validated['tingkat_lalu_lintas'],
            'panjang_ruas_rusak' => $validated['panjang_ruas_rusak'],
            'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
            'foto_kerusakan' => $fotoPath,
            'status_perbaikan' => 'belum diperbaiki',
            'klasifikasi_prioritas' => $prioritasKlasifikasi,
        ]);

        return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil ditambahkan dan diklasifikasikan sebagai prioritas ' . ucfirst($prioritasKlasifikasi) . '!');
    }

    /**
     * Display the specified resource.
     */
    public function show(KerusakanJalan $kerusakanJalan)
    {
        $kerusakanJalan->load(['jalan.regional', 'user']);
        return view('kerusakan_jalan.show', compact('kerusakanJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KerusakanJalan $kerusakanJalan)
    {
        $jalans = Jalan::all();
        return view('kerusakan_jalan.edit', compact('kerusakanJalan', 'jalans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KerusakanJalan $kerusakanJalan, NaiveBayesClassifier $classifier)
    {
        $validated = $request->validate([
            'jalan_id' => 'required|exists:jalan,id',
            'tanggal_lapor' => 'required|date',
            'tingkat_kerusakan' => ['required', 'string', Rule::in(['ringan', 'sedang', 'berat'])],
            'tingkat_lalu_lintas' => ['required', 'string', Rule::in(['rendah', 'sedang', 'tinggi'])],
            'panjang_ruas_rusak' => 'required|numeric|min:0',
            'deskripsi_kerusakan' => 'nullable|string|max:1000',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'status_perbaikan' => ['required', Rule::in(['belum diperbaiki', 'dalam perbaikan', 'sudah diperbaiki'])],
            'klasifikasi_prioritas' => ['nullable', Rule::in(['tinggi', 'sedang', 'rendah'])],
        ]);

        $fotoPath = $kerusakanJalan->foto_kerusakan;

        if ($request->hasFile('foto_kerusakan')) {
            if ($kerusakanJalan->foto_kerusakan) {
                Storage::disk('public')->delete($kerusakanJalan->foto_kerusakan);
            }
            $fotoPath = $request->file('foto_kerusakan')->store('kerusakan_jalan_photos', 'public');
        }

        $prioritasKlasifikasi = $classifier->classify(
            $validated['tingkat_kerusakan'],
            $validated['tingkat_lalu_lintas'],
            $validated['panjang_ruas_rusak']
        );

        $kerusakanJalan->update([
            'jalan_id' => $validated['jalan_id'],
            'tanggal_lapor' => $validated['tanggal_lapor'],
            'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
            'tingkat_lalu_lintas' => $validated['tingkat_lalu_lintas'],
            'panjang_ruas_rusak' => $validated['panjang_ruas_rusak'],
            'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
            'foto_kerusakan' => $fotoPath,
            'status_perbaikan' => $validated['status_perbaikan'],
            'klasifikasi_prioritas' => $prioritasKlasifikasi,
        ]);

        return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil diperbarui dan diklasifikasikan sebagai prioritas ' . ucfirst($prioritasKlasifikasi) . '!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KerusakanJalan $kerusakanJalan)
    {
        if ($kerusakanJalan->foto_kerusakan) {
            Storage::disk('public')->delete($kerusakanJalan->foto_kerusakan);
        }
        $kerusakanJalan->delete();

        return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil dihapus!');
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

    /**
     * Export all KerusakanJalan data to PDF.
     */
    public function exportPdf()
    {
        $kerusakanJalans = KerusakanJalan::with(['jalan.regional', 'user'])->latest('tanggal_lapor')->get();

        $pdf = Pdf::loadView('reports.kerusakan_jalan_pdf', compact('kerusakanJalans'));
        return $pdf->download('laporan_kerusakan_jalan_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Export all KerusakanJalan data to Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new KerusakanJalanExport, 'laporan_kerusakan_jalan_' . date('Ymd_His') . '.xlsx');
    }
}
