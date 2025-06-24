<?php

namespace App\Http\Controllers;

use App\Models\Jalan; // Import Model Jalan
use App\Models\KerusakanJalan; // Import Model KerusakanJalan
use Illuminate\Http\Request;
use Illuminate\View\View; // Import View

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function index(): View
    {
        return view('dashboard');
    }

    /**
     * Display the map overview of all roads and damages.
     */
    public function mapOverview(): View
    {
        // Ambil semua data jalan dengan relasi regional dan semua laporan kerusakannya
        $jalans = Jalan::with(['regional', 'kerusakanJalans' => function ($query) {
            $query->latest('tanggal_lapor'); // Ambil laporan kerusakan terbaru jika ada banyak
        }])->get();

        // Siapkan data dalam format yang mudah dikonsumsi oleh JavaScript
        $roadsGeoJson = [];
        foreach ($jalans as $jalan) {
            if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates']) && count($jalan->geometri_json['coordinates']) > 0) {
                // Tentukan warna berdasarkan kondisi paling parah atau prioritas tertinggi dari laporan kerusakan
                $color = 'blue'; // Warna default
                $priority = 'tidak ada';
                $damageLevel = $jalan->kondisi_jalan; // Kondisi awal jalan

                if ($jalan->kerusakanJalans->isNotEmpty()) {
                    // Ambil laporan kerusakan terbaru atau yang paling parah untuk menentukan warna
                    $latestDamage = $jalan->kerusakanJalans->first(); // Mengambil yang latest('tanggal_lapor')
                    $priority = $latestDamage->klasifikasi_prioritas ?? 'belum diklasifikasi';
                    $damageLevel = $latestDamage->tingkat_kerusakan;

                    switch ($priority) {
                        case 'tinggi':
                            $color = 'red';
                            break;
                        case 'sedang':
                            $color = 'orange';
                            break;
                        case 'rendah':
                            $color = 'green';
                            break;
                        default:
                            $color = 'gray'; // Jika belum diklasifikasi
                            break;
                    }
                }

                $roadsGeoJson[] = [
                    "type" => "Feature",
                    "properties" => [
                        "id" => $jalan->id,
                        "nama_jalan" => $jalan->nama_jalan,
                        "panjang_jalan" => $jalan->panjang_jalan,
                        "kondisi_awal" => $jalan->kondisi_jalan,
                        "regional" => $jalan->regional->nama_regional ?? 'N/A',
                        "regional_tipe" => $jalan->regional->tipe_regional ?? 'N/A',
                        "tingkat_kerusakan_terbaru" => $damageLevel, // Dari laporan atau kondisi awal
                        "prioritas_klasifikasi" => $priority,
                        "laporan_kerusakan" => $jalan->kerusakanJalans->map(function ($laporan) {
                            return [
                                'id' => $laporan->id,
                                'tanggal_lapor' => $laporan->tanggal_lapor->format('d M Y'),
                                'tingkat_kerusakan' => $laporan->tingkat_kerusakan,
                                'tingkat_lalu_lintas' => $laporan->tingkat_lalu_lintas,
                                'panjang_ruas_rusak' => $laporan->panjang_ruas_rusak,
                                'deskripsi' => $laporan->deskripsi_kerusakan,
                                'prioritas' => $laporan->klasifikasi_prioritas,
                                'status_perbaikan' => $laporan->status_perbaikan,
                                'pelapor' => $laporan->user->name ?? 'N/A',
                                'foto_url' => $laporan->foto_kerusakan ? asset('storage/' . $laporan->foto_kerusakan) : null,
                            ];
                        })->toArray(), // Pastikan ini juga diubah menjadi array
                    ],
                    "geometry" => $jalan->geometri_json // GeoJSON LineString lengkap dari DB
                ];
            }
        }

        return view('dashboard.map', compact('roadsGeoJson'));
    }
}
