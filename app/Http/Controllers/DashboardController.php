<?php

namespace App\Http\Controllers;

use App\Models\Jalan;
use App\Models\KerusakanJalan;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    // public function index(): View
    // {
    //     // Statistik Umum
    //     $totalJalan = Jalan::count();
    //     $totalLaporanKerusakan = KerusakanJalan::count();
    //     $totalUsers = User::count();

    //     // Statistik Laporan Berdasarkan Prioritas
    //     $prioritasLaporan = KerusakanJalan::select('klasifikasi_prioritas', DB::raw('count(*) as total'))
    //         ->groupBy('klasifikasi_prioritas')
    //         ->get()
    //         ->keyBy('klasifikasi_prioritas');

    //     $prioritasTinggi = $prioritasLaporan['tinggi']->total ?? 0;
    //     $prioritasSedang = $prioritasLaporan['sedang']->total ?? 0;
    //     $prioritasRendah = $prioritasLaporan['rendah']->total ?? 0;
    //     $prioritasBelumDiklasifikasi = $totalLaporanKerusakan - ($prioritasTinggi + $prioritasSedang + $prioritasRendah);

    //     // Statistik Laporan Berdasarkan Status Perbaikan
    //     $statusPerbaikan = KerusakanJalan::select('status_perbaikan', DB::raw('count(*) as total'))
    //         ->groupBy('status_perbaikan')
    //         ->get()
    //         ->keyBy('status_perbaikan');

    //     $statusBelumDiperbaiki = $statusPerbaikan['belum diperbaiki']->total ?? 0;
    //     $statusDalamPerbaikan = $statusPerbaikan['dalam perbaikan']->total ?? 0;
    //     $statusSudahDiperbaiki = $statusPerbaikan['sudah diperbaiki']->total ?? 0;

    //     // Statistik Pengguna Berdasarkan Role
    //     $userRoles = User::select('role', DB::raw('count(*) as total'))
    //         ->groupBy('role')
    //         ->get()
    //         ->keyBy('role');

    //     $adminUsers = $userRoles['admin']->total ?? 0;
    //     $pejabatDesaUsers = $userRoles['pejabat_desa']->total ?? 0;

    //     // --- Data untuk Line Chart Laporan Per Bulan (6 Bulan Terakhir) ---
    //     $months = [];
    //     $reportsPerMonth = [];
    //     for ($i = 5; $i >= 0; $i--) {
    //         $month = Carbon::now()->subMonths($i);
    //         $monthName = $month->translatedFormat('M Y');

    //         $count = KerusakanJalan::whereYear('tanggal_lapor', $month->year)
    //             ->whereMonth('tanggal_lapor', $month->month)
    //             ->count();

    //         $months[] = $monthName;
    //         $reportsPerMonth[] = $count;
    //     }

    //     // --- Data untuk Peta Mini (Semua Jalan) ---
    //     $jalansForDashboardMap = Jalan::with(['regional', 'rwRegional', 'dusunRegional', 'kerusakanJalans' => function ($query) {
    //         $query->latest('tanggal_lapor');
    //     }])->get();

    //     $roadsGeoJsonForDashboardMap = [];
    //     foreach ($jalansForDashboardMap as $jalan) {
    //         if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates']) && count($jalan->geometri_json['coordinates']) > 0) {
    //             $color = 'blue'; // Warna default
    //             $priority = 'tidak ada';
    //             $damageLevel = $jalan->kondisi_jalan;

    //             if ($jalan->kerusakanJalans->isNotEmpty()) {
    //                 $latestDamage = $jalan->kerusakanJalans->first();
    //                 $priority = $latestDamage->klasifikasi_prioritas ?? 'belum diklasifikasi';
    //                 $damageLevel = $latestDamage->tingkat_kerusakan;

    //                 switch ($priority) {
    //                     case 'tinggi':
    //                         $color = 'red';
    //                         break;
    //                     case 'sedang':
    //                         $color = 'orange';
    //                         break;
    //                     case 'rendah':
    //                         $color = 'green';
    //                         break;
    //                     default:
    //                         $color = 'gray';
    //                         break;
    //                 }
    //             } else {
    //                 switch ($jalan->kondisi_jalan) {
    //                     case 'rusak berat':
    //                         $color = 'red';
    //                         break;
    //                     case 'rusak sedang':
    //                         $color = 'orange';
    //                         break;
    //                     case 'rusak ringan':
    //                         $color = 'yellow';
    //                         break;
    //                     case 'baik':
    //                         $color = 'green';
    //                         break;
    //                     default:
    //                         $color = 'blue';
    //                         break;
    //                 }
    //             }

    //             $roadsGeoJsonForDashboardMap[] = [
    //                 "type" => "Feature",
    //                 "properties" => [
    //                     "id" => $jalan->id,
    //                     "nama_jalan" => $jalan->nama_jalan,
    //                     "panjang_jalan" => $jalan->panjang_jalan,
    //                     "kondisi_awal" => $jalan->kondisi_jalan,
    //                     "regional" => $jalan->regional->nama_regional ?? 'N/A',
    //                     "regional_tipe" => $jalan->regional->tipe_tipe_regional ?? 'N/A',
    //                     "tingkat_kerusakan_terbaru" => $damageLevel,
    //                     "prioritas_klasifikasi" => $priority,
    //                     "color" => $color,
    //                 ],
    //                 "geometry" => $jalan->geometri_json
    //             ];
    //         }
    //     }


    //     return view('dashboard', compact(
    //         'totalJalan',
    //         'totalLaporanKerusakan',
    //         'totalUsers',
    //         'prioritasTinggi',
    //         'prioritasSedang',
    //         'prioritasRendah',
    //         'prioritasBelumDiklasifikasi',
    //         'statusBelumDiperbaiki',
    //         'statusDalamPerbaikan',
    //         'statusSudahDiperbaiki',
    //         'adminUsers',
    //         'pejabatDesaUsers',
    //         'months',
    //         'reportsPerMonth',
    //         'roadsGeoJsonForDashboardMap' // <--- PASTIKAN INI ADA DI SINI
    //     ));
    // }
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

        // Data untuk Line Chart (6 Bulan Terakhir)
        $months = [];
        $reportsPerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->translatedFormat('M Y');

            $count = KerusakanJalan::whereYear('tanggal_lapor', $month->year)
                ->whereMonth('tanggal_lapor', $month->month)
                ->count();

            $months[] = $monthName;
            $reportsPerMonth[] = $count;
        }

        // Data untuk Peta Mini
        $jalansForDashboardMap = Jalan::with(['regional', 'rwRegional', 'dusunRegional', 'kerusakanJalans' => function ($query) {
            $query->latest('tanggal_lapor');
        }])->get();

        $roadsGeoJsonForMiniMap = [];
        foreach ($jalansForDashboardMap as $jalan) {
            if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates']) && count($jalan->geometri_json['coordinates']) > 0) {
                $color = 'blue';
                $priority = 'tidak ada';
                $damageLevel = $jalan->kondisi_jalan;

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
                            $color = 'gray';
                            break;
                    }
                } else {
                    switch ($jalan->kondisi_jalan) {
                        case 'rusak berat':
                            $color = 'red';
                            break;
                        case 'rusak sedang':
                            $color = 'orange';
                            break;
                        case 'rusak ringan':
                            $color = 'yellow';
                            break;
                        case 'baik':
                            $color = 'green';
                            break;
                        default:
                            $color = 'blue';
                            break;
                    }
                }

                $roadsGeoJsonForMiniMap[] = [
                    "type" => "Feature",
                    "properties" => [
                        "id" => $jalan->id,
                        "nama_jalan" => $jalan->nama_jalan,
                        "panjang_jalan" => $jalan->panjang_jalan,
                        "kondisi_awal" => $jalan->kondisi_jalan,
                        "regional" => $jalan->regional->nama_regional ?? 'N/A',
                        "regional_tipe" => $jalan->regional->tipe_tipe_regional ?? 'N/A',
                        "tingkat_kerusakan_terbaru" => $damageLevel,
                        "prioritas_klasifikasi" => $priority,
                        "color" => $color,
                    ],
                    "geometry" => $jalan->geometri_json
                ];
            }
        }

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
            'pejabatDesaUsers',
            'months',
            'reportsPerMonth',
            'roadsGeoJsonForMiniMap' // ✅ variabel disamakan dengan blade
        ));
    }


    /**
     * Display the map overview of all roads and damages.
     */
    public function mapOverview(Request $request): View
    {
        $filterRegionalId = $request->query('regional_id');
        $filterPrioritas = $request->query('prioritas');
        $filterStatusPerbaikan = $request->query('status_perbaikan');

        $queryJalan = Jalan::with(['regional', 'kerusakanJalans' => function ($query) {
            $query->latest('tanggal_lapor');
        }]);

        if ($filterPrioritas || $filterStatusPerbaikan) {
            $queryJalan->whereHas('kerusakanJalans', function ($query) use ($filterPrioritas, $filterStatusPerbaikan) {
                if ($filterPrioritas) {
                    if ($filterPrioritas === 'belum_diklasifikasi') {
                        $query->whereNull('klasifikasi_prioritas');
                    } else {
                        $query->where('klasifikasi_prioritas', $filterPrioritas);
                    }
                }
                if ($filterStatusPerbaikan) {
                    $query->where('status_perbaikan', str_replace('_', ' ', $filterStatusPerbaikan));
                }
            });
        }

        if ($filterRegionalId) {
            $queryJalan->where(function ($query) use ($filterRegionalId) {
                $query->where('regional_id', $filterRegionalId)
                    ->orWhere('rw_regional_id', $filterRegionalId)
                    ->orWhere('dusun_regional_id', $filterRegionalId);
            });
        }

        $jalans = $queryJalan->get();

        $roadsGeoJson = [];
        foreach ($jalans as $jalan) {
            if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates']) && count($jalan->geometri_json['coordinates']) > 0) {
                $color = 'blue';
                $priority = 'tidak ada';
                $damageLevel = $jalan->kondisi_jalan;

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
                            $color = 'gray';
                            break;
                    }
                } else {
                    switch ($jalan->kondisi_jalan) {
                        case 'rusak berat':
                            $color = 'red';
                            break;
                        case 'rusak sedang':
                            $color = 'orange';
                            break;
                        case 'rusak ringan':
                            $color = 'yellow';
                            break;
                        case 'baik':
                            $color = 'green';
                            break;
                        default:
                            $color = 'blue';
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
                        "regional_tipe" => $jalan->regional->tipe_tipe_regional ?? 'N/A',
                        "tingkat_kerusakan_terbaru" => $damageLevel,
                        "prioritas_klasifikasi" => $priority,
                        "color" => $color,
                    ],
                    "geometry" => $jalan->geometri_json
                ];
            }
        }

        $regionals = Regional::all();

        return view('dashboard.map', compact('roadsGeoJson', 'regionals', 'filterRegionalId', 'filterPrioritas', 'filterStatusPerbaikan'));
    }
}
