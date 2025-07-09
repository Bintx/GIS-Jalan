<?php

namespace App\Http\Controllers;

use App\Models\Jalan;
use App\Models\KerusakanJalan;
use App\Models\Regional; // Pastikan ini diimpor
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
    public function index(): View
    {
        // Statistik Umum
        $totalJalan = Jalan::count();
        $totalLaporanKerusakan = KerusakanJalan::count();
        $totalUsers = User::count();
        $totalRegional = Regional::count();

        // Tambahkan penghitungan spesifik untuk RT, RW, Dusun
        // KOREKSI: UBAH 'tipe_tipe_regional' menjadi 'tipe_regional'
        $totalRt = Regional::where('tipe_regional', 'RT')->count();
        $totalRw = Regional::where('tipe_regional', 'RW')->count();
        $totalDusun = Regional::where('tipe_regional', 'Dusun')->count();


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

        // Data untuk Peta Mini (disamakan dengan mapOverview)
        $jalansForDashboardMap = Jalan::with([
            'regional',
            'rwRegional', // Memuat relasi rwRegional
            'dusunRegional', // Memuat relasi dusunRegional
            'kerusakanJalans' => function ($query) {
                // Fetch all related damage reports, ordered by date
                $query->orderBy('tanggal_lapor', 'desc')
                    ->select('id', 'jalan_id', 'tanggal_lapor', 'tingkat_kerusakan', 'tingkat_lalu_lintas', 'panjang_ruas_rusak', 'deskripsi_kerusakan', 'foto_kerusakan', 'status_perbaikan', 'klasifikasi_prioritas');
            }
        ])->get();

        $roadsGeoJsonForMiniMap = [];
        foreach ($jalansForDashboardMap as $jalan) {
            if ($jalan->geometri_json && is_array($jalan->geometri_json) && isset($jalan->geometri_json['coordinates']) && count($jalan->geometri_json['coordinates']) > 0) {
                $color = 'blue';
                $priority = 'tidak ada';
                $damageLevel = $jalan->kondisi_jalan;
                $allDamageReports = []; // Inisialisasi array untuk semua laporan kerusakan

                if ($jalan->kerusakanJalans->isNotEmpty()) {
                    $latestDamage = $jalan->kerusakanJalans->first(); // Get the latest for map color
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

                    // Siapkan semua laporan kerusakan untuk popup
                    foreach ($jalan->kerusakanJalans as $report) {
                        $allDamageReports[] = [
                            'id' => $report->id, // Tambahkan baris ini untuk memastikan ID dikirim ke frontend
                            'tanggal_lapor' => $report->tanggal_lapor->format('d M Y'),
                            'tingkat_kerusakan' => $report->tingkat_kerusakan,
                            'tingkat_lalu_lintas' => $report->tingkat_lalu_lintas,
                            'panjang_ruas_rusak' => $report->panjang_ruas_rusak,
                            'deskripsi_kerusakan' => $report->deskripsi_kerusakan,
                            'foto_url' => $report->foto_kerusakan ? asset('storage/' . $report->foto_kerusakan) : null,
                            'status_perbaikan' => $report->status_perbaikan,
                            'prioritas' => $report->klasifikasi_prioritas,
                        ];
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
                        "rw_regional" => $jalan->rwRegional->nama_regional ?? 'N/A',
                        "dusun_regional" => $jalan->dusunRegional->nama_regional ?? 'N/A',
                        "tingkat_kerusakan_terbaru" => $damageLevel,
                        "prioritas_klasifikasi" => $priority,
                        "color" => $color,
                        "laporan_kerusakan" => $allDamageReports, // Teruskan semua laporan kerusakan
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
            'roadsGeoJsonForMiniMap',
            'totalRegional',
            'totalRt', // Tambahkan ini
            'totalRw', // Tambahkan ini
            'totalDusun' // Tambahkan ini
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

        $queryJalan = Jalan::with([
            'regional',
            'rwRegional', // Memuat relasi rwRegional
            'dusunRegional', // Memuat relasi dusunRegional
            'kerusakanJalans' => function ($query) {
                // Fetch all related damage reports, ordered by date
                $query->orderBy('tanggal_lapor', 'desc')
                    ->select('id', 'jalan_id', 'tanggal_lapor', 'tingkat_kerusakan', 'tingkat_lalu_lintas', 'panjang_ruas_rusak', 'deskripsi_kerusakan', 'foto_kerusakan', 'status_perbaikan', 'klasifikasi_prioritas');
            }
        ]);

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
                $allDamageReports = []; // Inisialisasi array untuk semua laporan kerusakan

                if ($jalan->kerusakanJalans->isNotEmpty()) {
                    $latestDamage = $jalan->kerusakanJalans->first(); // Get the latest for map color
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

                    // Siapkan semua laporan kerusakan untuk popup
                    foreach ($jalan->kerusakanJalans as $report) {
                        $allDamageReports[] = [
                            'id' => $report->id, // Tambahkan baris ini untuk memastikan ID dikirim ke frontend
                            'tanggal_lapor' => $report->tanggal_lapor->format('d M Y'),
                            'tingkat_kerusakan' => $report->tingkat_kerusakan,
                            'tingkat_lalu_lintas' => $report->tingkat_lalu_lintas,
                            'panjang_ruas_rusak' => $report->panjang_ruas_rusak,
                            'deskripsi_kerusakan' => $report->deskripsi_kerusakan,
                            'foto_url' => $report->foto_kerusakan ? asset('storage/' . $report->foto_kerusakan) : null,
                            'status_perbaikan' => $report->status_perbaikan,
                            'prioritas' => $report->klasifikasi_prioritas,
                        ];
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
                        "rw_regional" => $jalan->rwRegional->nama_regional ?? 'N/A', // Tambah RW Regional
                        "dusun_regional" => $jalan->dusunRegional->nama_regional ?? 'N/A', // Tambah Dusun Regional
                        "tingkat_kerusakan_terbaru" => $damageLevel,
                        "prioritas_klasifikasi" => $priority,
                        "color" => $color,
                        "laporan_kerusakan" => $allDamageReports, // Teruskan semua laporan kerusakan
                    ],
                    "geometry" => $jalan->geometri_json
                ];
            }
        }

        $regionals = Regional::all();

        return view('dashboard.map', compact('roadsGeoJson', 'regionals', 'filterRegionalId', 'filterPrioritas', 'filterStatusPerbaikan'));
    }
}
