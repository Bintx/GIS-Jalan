<?php

namespace App\Http\Controllers;

use App\Models\Jalan;
use App\Models\KerusakanJalan;
use App\Models\Regional; // Import Model Regional untuk filter
use App\Models\User;
use Illuminate\Http\Request; // Import Request
use Illuminate\Support\Facades\DB; // Tambahkan baris ini
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function index(): View
    {
        // Statistik Umum
        $totalJalan = Jalan::count();
        $totalLaporanKerusakan = KerusakanJalan::count();
        $totalUsers = User::count();

        // Statistik Laporan Berdasarkan Prioritas
        $prioritasLaporan = KerusakanJalan::select('klasifikasi_prioritas', DB::raw('count(*) as total'))
            ->groupBy('klasifikasi_prioritas')
            ->get()
            ->keyBy('klasifikasi_prioritas');

        $prioritasTinggi = $prioritasLaporan['tinggi']->total ?? 0;
        $prioritasSedang = $prioritasLaporan['sedang']->total ?? 0;
        $prioritasRendah = $prioritasLaporan['rendah']->total ?? 0;
        $prioritasBelumDiklasifikasi = $totalLaporanKerusakan - ($prioritasTinggi + $prioritasSedang + $prioritasRendah);

        // Statistik Laporan Berdasarkan Status Perbaikan
        $statusPerbaikan = KerusakanJalan::select('status_perbaikan', DB::raw('count(*) as total'))
            ->groupBy('status_perbaikan')
            ->get()
            ->keyBy('status_perbaikan');

        $statusBelumDiperbaiki = $statusPerbaikan['belum diperbaiki']->total ?? 0;
        $statusDalamPerbaikan = $statusPerbaikan['dalam perbaikan']->total ?? 0;
        $statusSudahDiperbaiki = $statusPerbaikan['sudah diperbaiki']->total ?? 0;

        // Statistik Pengguna Berdasarkan Role
        $userRoles = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get()
            ->keyBy('role');

        $adminUsers = $userRoles['admin']->total ?? 0;
        $pejabatDesaUsers = $userRoles['pejabat_desa']->total ?? 0;


        return view('dashboard', compact(
            'totalJalan',
            'totalLaporanKerusakan',
            'totalUsers',
            'prioritasTinggi',
            'prioritasSedang',
            'prioritasRendah',
            'prioritasBelumDiklasifikasi',
            'statusBelumDiperbaiki',
            'statusDalamPerbaikan',
            'statusSudahDiperbaiki',
            'adminUsers',
            'pejabatDesaUsers'
        ));
    }

    /**
     * Display the map overview of all roads and damages.
     */
    public function mapOverview(Request $request): View
    {
        // Ambil filter dari request
        $filterRegionalId = $request->query('regional_id');
        $filterPrioritas = $request->query('prioritas'); // tinggi, sedang, rendah, belum_diklasifikasi
        $filterStatusPerbaikan = $request->query('status_perbaikan'); // belum_diperbaiki, dalam_perbaikan, sudah_diperbaiki

        // Query Jalan
        $queryJalan = Jalan::with(['regional', 'kerusakanJalans' => function ($query) use ($filterPrioritas, $filterStatusPerbaikan) {
            $query->latest('tanggal_lapor'); // Prioritaskan laporan terbaru

            // Filter laporan berdasarkan prioritas (jika ada)
            if ($filterPrioritas) {
                if ($filterPrioritas === 'belum_diklasifikasi') {
                    $query->whereNull('klasifikasi_prioritas');
                } else {
                    $query->where('klasifikasi_prioritas', $filterPrioritas);
                }
            }

            // Filter laporan berdasarkan status perbaikan (jika ada)
            if ($filterStatusPerbaikan) {
                $query->where('status_perbaikan', str_replace('_', ' ', $filterStatusPerbaikan)); // Konversi underscore ke spasi
            }
        }]);

        // Filter Jalan berdasarkan Regional (jika ada)
        if ($filterRegionalId) {
            $queryJalan->where('regional_id', $filterRegionalId);
        }

        $jalans = $queryJalan->get(); // Eksekusi query

        $roadsGeoJson = [];
        foreach ($jalans as $jalan) {
            // Hanya tampilkan jalan yang memiliki geometri yang valid
            if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates']) && count($jalan->geometri_json['coordinates']) > 0) {
                $color = 'blue'; // Warna default untuk jalan tanpa laporan atau kondisi baik
                $priority = 'tidak ada';
                $damageLevel = $jalan->kondisi_jalan; // Kondisi awal dari master jalan

                // Logic penentuan warna dan prioritas dari laporan kerusakan
                if ($jalan->kerusakanJalans->isNotEmpty()) {
                    $latestDamage = $jalan->kerusakanJalans->first();
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
                } else {
                    // Jika tidak ada laporan kerusakan, gunakan kondisi awal jalan untuk menentukan warna dasar
                    switch ($jalan->kondisi_jalan) {
                        case 'rusak berat':
                            $color = 'red'; // Merah untuk rusak berat
                            break;
                        case 'rusak sedang':
                            $color = 'orange'; // Oranye untuk rusak sedang
                            break;
                        case 'rusak ringan':
                            $color = 'yellow'; // Kuning untuk rusak ringan
                            break;
                        case 'baik':
                            $color = 'green'; // Hijau untuk baik
                            break;
                        default:
                            $color = 'blue'; // Default jika kondisi tidak terdefinisi
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
                        "tingkat_kerusakan_terbaru" => $damageLevel,
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
                        })->toArray(),
                    ],
                    "geometry" => $jalan->geometri_json
                ];
            }
        }

        // Ambil semua regional untuk filter dropdown
        $regionals = Regional::all();

        // Teruskan data ke view, termasuk filter yang aktif saat ini
        return view('dashboard.map', compact('roadsGeoJson', 'regionals', 'filterRegionalId', 'filterPrioritas', 'filterStatusPerbaikan'));
    }
}
