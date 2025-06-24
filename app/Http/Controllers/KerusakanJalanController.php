<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KerusakanJalan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // Untuk upload file
use App\Models\Jalan; // Perlu untuk dropdown Jalan di form laporan
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang login
use App\Services\NaiveBayesClassifier;

class KerusakanJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua laporan kerusakan jalan, dengan relasi jalan dan user
        $kerusakanJalans = KerusakanJalan::with(['jalan', 'user'])->latest()->paginate(10);
        return view('kerusakan_jalan.index', compact('kerusakanJalans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua data jalan untuk dropdown di form laporan
        $jalans = Jalan::all();
        return view('kerusakan_jalan.create', compact('jalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'jalan_id' => 'required|exists:jalan,id',
    //         'tanggal_lapor' => 'required|date',
    //         'tingkat_kerusakan' => ['required', 'string', Rule::in(['ringan', 'sedang', 'berat'])],
    //         'tingkat_lalu_lintas' => ['required', 'string', Rule::in(['rendah', 'sedang', 'tinggi'])],
    //         'panjang_ruas_rusak' => 'required|numeric|min:0',
    //         'deskripsi_kerusakan' => 'nullable|string|max:1000',
    //         'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
    //     ]);

    //     $fotoPath = null;
    //     if ($request->hasFile('foto_kerusakan')) {
    //         // Simpan foto ke direktori 'public/storage/kerusakan_jalan_photos'
    //         $fotoPath = $request->file('foto_kerusakan')->store('kerusakan_jalan_photos', 'public');
    //     }

    //     KerusakanJalan::create([
    //         'jalan_id' => $validated['jalan_id'],
    //         'user_id' => Auth::id(), // ID user yang sedang login
    //         'tanggal_lapor' => $validated['tanggal_lapor'],
    //         'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
    //         'tingkat_lalu_lintas' => $validated['tingkat_lalu_lintas'],
    //         'panjang_ruas_rusak' => $validated['panjang_ruas_rusak'],
    //         'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
    //         'foto_kerusakan' => $fotoPath, // Simpan path fotonya
    //         'status_perbaikan' => 'belum diperbaiki', // Status default
    //         // Kolom klasifikasi_prioritas akan diisi oleh algoritma Naive Bayes nanti
    //     ]);

    //     return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil ditambahkan!');
    // }
    public function store(Request $request, NaiveBayesClassifier $classifier) // Inject classifier
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

        // --- Lakukan Klasifikasi Naive Bayes di sini ---
        $prioritasKlasifikasi = $classifier->classify(
            $validated['tingkat_kerusakan'],
            $validated['tingkat_lalu_lintas'],
            $validated['panjang_ruas_rusak']
        );
        // --- Akhir Klasifikasi ---

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
            'klasifikasi_prioritas' => $prioritasKlasifikasi, // Simpan hasil klasifikasi
        ]);

        return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil ditambahkan dan diklasifikasikan sebagai prioritas ' . ucfirst($prioritasKlasifikasi) . '!');
    }

    /**
     * Display the specified resource.
     */
    public function show(KerusakanJalan $kerusakanJalan)
    {
        // Load relasi jalan dan user
        $kerusakanJalan->load(['jalan', 'user']);
        return view('kerusakan_jalan.show', compact('kerusakanJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KerusakanJalan $kerusakanJalan)
    {
        $jalans = Jalan::all(); // Ambil semua data jalan untuk dropdown
        return view('kerusakan_jalan.edit', compact('kerusakanJalan', 'jalans'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, KerusakanJalan $kerusakanJalan)
    // {
    //     $validated = $request->validate([
    //         'jalan_id' => 'required|exists:jalan,id',
    //         'tanggal_lapor' => 'required|date',
    //         'tingkat_kerusakan' => ['required', 'string', Rule::in(['ringan', 'sedang', 'berat'])],
    //         'tingkat_lalu_lintas' => ['required', 'string', Rule::in(['rendah', 'sedang', 'tinggi'])],
    //         'panjang_ruas_rusak' => 'required|numeric|min:0',
    //         'deskripsi_kerusakan' => 'nullable|string|max:1000',
    //         'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
    //         'status_perbaikan' => ['required', Rule::in(['belum diperbaiki', 'dalam perbaikan', 'sudah diperbaiki'])], // Bisa diupdate oleh admin
    //         'klasifikasi_prioritas' => ['nullable', Rule::in(['tinggi', 'sedang', 'rendah'])], // Bisa diupdate oleh admin (manual/sistem)
    //     ]);

    //     $fotoPath = $kerusakanJalan->foto_kerusakan; // Pertahankan foto lama secara default

    //     if ($request->hasFile('foto_kerusakan')) {
    //         // Hapus foto lama jika ada dan unggah yang baru
    //         if ($kerusakanJalan->foto_kerusakan) {
    //             Storage::disk('public')->delete($kerusakanJalan->foto_kerusakan);
    //         }
    //         $fotoPath = $request->file('foto_kerusakan')->store('kerusakan_jalan_photos', 'public');
    //     }

    //     $kerusakanJalan->update([
    //         'jalan_id' => $validated['jalan_id'],
    //         // user_id tidak diupdate karena pelapornya sama
    //         'tanggal_lapor' => $validated['tanggal_lapor'],
    //         'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
    //         'tingkat_lalu_lintas' => $validated['tingkat_lalu_lintas'],
    //         'panjang_ruas_rusak' => $validated['panjang_ruas_rusak'],
    //         'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
    //         'foto_kerusakan' => $fotoPath,
    //         'status_perbaikan' => $validated['status_perbaikan'],
    //         'klasifikasi_prioritas' => $validated['klasifikasi_prioritas'],
    //     ]);

    //     return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil diperbarui!');
    // }
    public function update(Request $request, KerusakanJalan $kerusakanJalan, NaiveBayesClassifier $classifier) // Inject classifier
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
            'klasifikasi_prioritas' => ['nullable', Rule::in(['tinggi', 'sedang', 'rendah'])], // Admin bisa update manual, atau sistem
        ]);

        $fotoPath = $kerusakanJalan->foto_kerusakan;

        if ($request->hasFile('foto_kerusakan')) {
            if ($kerusakanJalan->foto_kerusakan) {
                Storage::disk('public')->delete($kerusakanJalan->foto_kerusakan);
            }
            $fotoPath = $request->file('foto_kerusakan')->store('kerusakan_jalan_photos', 'public');
        }

        // --- Lakukan Klasifikasi Naive Bayes saat update juga (jika fitur input berubah) ---
        // Kecuali jika admin secara manual sudah memilih prioritas.
        // Atau Anda bisa menambahkan checkbox "re-classify" di form edit.
        // Untuk sederhana, kita akan selalu mengklasifikasi ulang berdasarkan input yang divalidasi.
        $prioritasKlasifikasi = $classifier->classify(
            $validated['tingkat_kerusakan'],
            $validated['tingkat_lalu_lintas'],
            $validated['panjang_ruas_rusak']
        );
        // --- Akhir Klasifikasi ---

        $kerusakanJalan->update([
            'jalan_id' => $validated['jalan_id'],
            'tanggal_lapor' => $validated['tanggal_lapor'],
            'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
            'tingkat_lalu_lintas' => $validated['tingkat_lalu_lintas'],
            'panjang_ruas_rusak' => $validated['panjang_ruas_rusak'],
            'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
            'foto_kerusakan' => $fotoPath,
            'status_perbaikan' => $validated['status_perbaikan'],
            // prioritaskan klasifikasi dari sistem jika tidak diisi manual oleh admin
            'klasifikasi_prioritas' => $prioritasKlasifikasi,
        ]);

        return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil diperbarui dan diklasifikasikan sebagai prioritas ' . ucfirst($prioritasKlasifikasi) . '!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KerusakanJalan $kerusakanJalan)
    {
        // Hapus foto terkait jika ada
        if ($kerusakanJalan->foto_kerusakan) {
            Storage::disk('public')->delete($kerusakanJalan->foto_kerusakan);
        }
        $kerusakanJalan->delete();

        return redirect()->route('kerusakan-jalan.index')->with('success', 'Laporan kerusakan jalan berhasil dihapus!');
    }
}
