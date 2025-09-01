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
    public function index(Request $request)
    {
        $filterNamaJalan = $request->query('nama_jalan');
        $filterTingkatKerusakan = $request->query('tingkat_kerusakan');
        $filterPrioritas = $request->query('prioritas');
        $filterStatusPerbaikan = $request->query('status_perbaikan');

        $queryLaporan = KerusakanJalan::with(['jalan.regional', 'user']);

        if ($filterNamaJalan) {
            $queryLaporan->whereHas('jalan', function ($query) use ($filterNamaJalan) {
                $query->where('nama_jalan', 'like', '%' . $filterNamaJalan . '%');
            });
        }
        if ($filterTingkatKerusakan) {
            $queryLaporan->where('tingkat_kerusakan', $filterTingkatKerusakan);
        }
        if ($filterPrioritas) {
            if ($filterPrioritas === 'belum_diklasifikasi') {
                $queryLaporan->whereNull('klasifikasi_prioritas');
            } else {
                $queryLaporan->where('klasifikasi_prioritas', $filterPrioritas);
            }
        }
        if ($filterStatusPerbaikan) {
            $queryLaporan->where('status_perbaikan', str_replace('_', ' ', $filterStatusPerbaikan));
        }

        $kerusakanJalans = $queryLaporan->latest()->get();
        $allJalanNames = Jalan::select('id', 'nama_jalan')->get();

        return view('kerusakan_jalan.index', compact(
            'kerusakanJalans',
            'allJalanNames',
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
        // Muat relasi regional, rwRegional, dan dusunRegional untuk setiap jalan
        $jalans = Jalan::with(['regional', 'rwRegional', 'dusunRegional'])->get(); // <-- PERUBAHAN DI SINI
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
        $validationRules = [
            'jalan_id' => 'required|exists:jalan,id',
            'tanggal_lapor' => 'required|date',
            'tingkat_kerusakan' => ['required', 'string', Rule::in(['ringan', 'sedang', 'berat'])],
            'tingkat_lalu_lintas' => ['required', 'string', Rule::in(['rendah', 'sedang', 'tinggi'])],
            'panjang_ruas_rusak' => 'required|numeric|min:0',
            'deskripsi_kerusakan' => 'nullable|string|max:1000',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ];

        if (Auth::user()->isAdmin()) {
            $validationRules['status_perbaikan'] = ['required', Rule::in(['belum diperbaiki', 'dalam perbaikan', 'sudah diperbaiki'])];
            $validationRules['klasifikasi_prioritas'] = ['nullable', Rule::in(['tinggi', 'sedang', 'rendah'])];
        }

        $validated = $request->validate($validationRules);

        $fotoPath = $kerusakanJalan->foto_kerusakan;

        if ($request->hasFile('foto_kerusakan')) {
            if ($kerusakanJalan->foto_kerusakan) {
                Storage::disk('public')->delete($kerusakanJalan->foto_kerusakan);
            }
            $fotoPath = $request->file('foto_kerusakan')->store('kerusakan_jalan_photos', 'public');
        }

        $dataToUpdate = [
            'jalan_id' => $validated['jalan_id'],
            'tanggal_lapor' => $validated['tanggal_lapor'],
            'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
            'tingkat_lalu_lintas' => $validated['tingkat_lalu_lintas'],
            'panjang_ruas_rusak' => $validated['panjang_ruas_rusak'],
            'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
            'foto_kerusakan' => $fotoPath,
        ];

        $prioritasKlasifikasi = $classifier->classify(
            $validated['tingkat_kerusakan'],
            $validated['tingkat_lalu_lintas'],
            $validated['panjang_ruas_rusak']
        );
        $dataToUpdate['klasifikasi_prioritas'] = $prioritasKlasifikasi;

        $dataToUpdate['status_perbaikan'] = Auth::user()->isAdmin() ? $validated['status_perbaikan'] : $kerusakanJalan->status_perbaikan;


        $kerusakanJalan->update($dataToUpdate);

        return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil diperbarui dan diklasifikasikan sebagai prioritas ' . ucfirst($prioritasKlasifikasi) . '!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KerusakanJalan $kerusakanJalan)
    {
        if (Auth::user()->isAdmin() || (Auth::user()->isPejabatDesa() && Auth::id() === $kerusakanJalan->user_id)) {
            if ($kerusakanJalan->foto_kerusakan) {
                Storage::disk('public')->delete($kerusakanJalan->foto_kerusakan);
            }
            $kerusakanJalan->delete();
            return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil dihapus!');
        }

        return redirect()->route('kerusakan-jalan.index')->with('error', 'Akses Dilarang. Anda tidak memiliki izin untuk menghapus laporan ini.');
    }

    /**
     * Get road data by ID for AJAX request.
     * Includes regional data for context.
     */
    public function getJalanData(Jalan $jalan)
    {
        // Muat relasi regional, rwRegional, dan dusunRegional
        $jalan->load(['regional', 'rwRegional', 'dusunRegional']);

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
            // Tambahkan nama regional lengkap
            'regional_rt_nama' => $jalan->regional->nama_regional ?? 'N/A',
            'regional_rw_nama' => $jalan->rwRegional->nama_regional ?? 'N/A',
            'regional_dusun_nama' => $jalan->dusunRegional->nama_regional ?? 'N/A',
            'suggested_tingkat_kerusakan' => $tingkatKerusakanMap[$jalan->kondisi_jalan] ?? '',
            'suggested_panjang_ruas_rusak' => $jalan->panjang_jalan,
        ]);
    }

    /**
     * Export all KerusakanJalan data to PDF.
     */
    public function exportPdf()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('kerusakan-jalan.index')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan untuk mengunduh laporan.');
        }

        $kerusakanJalans = KerusakanJalan::with(['jalan.regional', 'user'])->latest('tanggal_lapor')->get();

        $pdf = Pdf::loadView('reports.kerusakan_jalan_pdf', compact('kerusakanJalans'));
        return $pdf->download('laporan_kerusakan_jalan_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Export all KerusakanJalan data to Excel.
     */
    public function exportExcel()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('kerusakan-jalan.index')->with('error', 'Akses Dilarang. Hanya Admin yang diizinkan untuk mengunduh laporan.');
        }
        return Excel::download(new KerusakanJalanExport, 'laporan_kerusakan_jalan_' . date('Ymd_His') . '.xlsx');
    }
}
