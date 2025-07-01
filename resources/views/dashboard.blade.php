@extends('layouts.app')

@section('title', 'Dashboard - Sistem Informasi Perbaikan Jalan')

@section('content')
    @push('styles')
        <style>
            #miniMap {
                height: 300px;
                /* Atau sesuaikan dengan tinggi yang Anda inginkan */
                width: 100%;
                border-radius: 8px;
                z-index: 1;
            }

            .leaflet-container {
                background: #fff;
            }

            /* Styling untuk info-box di pop-up peta */
            .info-box {
                background: white;
                padding: 10px;
                border-radius: 5px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                font-size: 14px;
                line-height: 1.5;
            }

            .info-box h5 {
                margin-top: 0;
                font-size: 16px;
                color: #333;
            }

            .info-box .priority-badge {
                font-weight: bold;
                padding: 4px 8px;
                border-radius: 3px;
                color: white;
                display: inline-block;
                margin-bottom: 5px;
                text-transform: uppercase;
            }

            .info-box .priority-high {
                background-color: #dc3545;
            }

            .info-box .priority-medium {
                background-color: #ffc107;
                color: #212529;
            }

            .info-box .priority-low {
                background-color: #28a745;
            }

            .info-box .priority-unclassified {
                background-color: #6c757d;
            }

            .info-box .status-badge {
                font-weight: bold;
                padding: 4px 8px;
                border-radius: 3px;
                color: white;
                display: inline-block;
                margin-bottom: 5px;
                text-transform: uppercase;
            }

            .info-box .status-belum {
                background-color: #dc3545;
            }

            .info-box .status-dalam {
                background-color: #ffc107;
                color: #212529;
            }

            .info-box .status-sudah {
                background-color: #28a745;
            }
        </style>
    @endpush

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Dashboard Overview</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Sistem Informasi Jalan Rusak</li>
        </ul>
    </div>

    {{-- Bagian Statistik Umum --}}
    <div class="row row-cols-xxl-3 row-cols-lg-3 row-cols-md-2 row-cols-sm-1 gy-4 mb-4">
        {{-- Total Jalan Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-1 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Jalan</p>
                            <h6 class="mb-0">{{ $totalJalan }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="material-symbols:route" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Laporan Kerusakan Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Laporan Kerusakan</p>
                            <h6 class="mb-0">{{ $totalLaporanKerusakan }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:danger-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Pengguna Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-3 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Pengguna</p>
                            <h6 class="mb-0">{{ $totalUsers }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Admin Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-5 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Admin</p>
                            <h6 class="mb-0">{{ $adminUsers }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:user-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Pejabat Desa Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Pejabat Desa</p>
                            <h6 class="mb-0">{{ $pejabatDesaUsers }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="fluent:people-20-filled" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-xxl-3 row-cols-lg-2 row-cols-1 gy-4 mt-4">
        {{-- Statistik Prioritas Laporan (List) --}}
        <div class="col">
            <div class="card h-100 border">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-3">Laporan Berdasarkan Prioritas</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Prioritas Tinggi
                            <span class="badge bg-danger rounded-pill">{{ $prioritasTinggi }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Prioritas Sedang
                            <span class="badge bg-warning rounded-pill">{{ $prioritasSedang }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Prioritas Rendah
                            <span class="badge bg-success rounded-pill">{{ $prioritasRendah }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Belum Diklasifikasi
                            <span class="badge bg-secondary rounded-pill">{{ $prioritasBelumDiklasifikasi }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Statistik Status Perbaikan (List) --}}
        <div class="col">
            <div class="card h-100 border">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-3">Laporan Berdasarkan Status Perbaikan</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Belum Diperbaiki
                            <span class="badge bg-danger rounded-pill">{{ $statusBelumDiperbaiki }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dalam Perbaikan
                            <span class="badge bg-warning rounded-pill">{{ $statusDalamPerbaikan }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sudah Diperbaiki
                            <span class="badge bg-success rounded-pill">{{ $statusSudahDiperbaiki }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        {{-- Chart Distribusi Prioritas (Donut Chart) --}}
        <div class="col-xxl-6 col-xl-12">
            <div class="card h-100 border">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-3">Distribusi Laporan Berdasarkan Prioritas</h6>
                    <div id="priorityDistributionChart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>

        {{-- Line Chart Laporan Per Bulan --}}
        <div class="col-xxl-6 col-xl-12"> {{-- Mengambil sisa ruang --}}
            <div class="card h-100 border">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-3">Jumlah Laporan Per Bulan</h6>
                    <div id="reportsPerMonthChart" style="min-height: 250px;"></div> {{-- ID untuk chart --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Peta Overview Jalan - Disesuaikan untuk lebar penuh dan tinggi lebih besar --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card h-100 border">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-3">Peta Overview Jalan</h6>
                    <div id="miniMap" style="height: 600px; width: 100%; border-radius: 8px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/lib/leaflet.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Data untuk Chart Distribusi Prioritas (Donut Chart)
            var prioritasData = {
                tinggi: {{ $prioritasTinggi }},
                sedang: {{ $prioritasSedang }},
                rendah: {{ $prioritasRendah }},
                belumDiklasifikasi: {{ $prioritasBelumDiklasifikasi }}
            };

            var optionsDonut = {
                series: [
                    prioritasData.tinggi,
                    prioritasData.sedang,
                    prioritasData.rendah,
                    prioritasData.belumDiklasifikasi
                ],
                chart: {
                    type: 'donut',
                    height: 250
                },
                labels: ['Tinggi', 'Sedang', 'Rendah', 'Belum Diklasifikasi'],
                colors: ['#dc3545', '#ffc107', '#28a745', '#6c757d'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'right',
                    offsetY: 0,
                    height: 230,
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return opts.w.globals.series[opts.seriesIndex]
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " Laporan"
                        }
                    }
                }
            };

            var chartDonut = new ApexCharts(document.querySelector("#priorityDistributionChart"), optionsDonut);
            chartDonut.render();

            // --- Data dan Opsi untuk Line Chart Laporan Per Bulan ---
            var months = {!! json_encode($months) !!};
            var reportsPerMonth = {!! json_encode($reportsPerMonth) !!};

            var optionsLine = {
                series: [{
                    name: "Jumlah Laporan",
                    data: reportsPerMonth
                }],
                chart: {
                    height: 250,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: months,
                    labels: {
                        rotate: -45,
                        rotateAlways: true
                    }
                },
                yaxis: {
                    title: {
                        text: ''
                    },
                    min: 0,
                    max: 10, // Sesuaikan max ini jika Anda memiliki lebih dari 10 laporan per bulan
                    tickAmount: 5
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " Laporan"
                        }
                    }
                },
                colors: ['#007bff']
            };

            var chartLine = new ApexCharts(document.querySelector("#reportsPerMonthChart"), optionsLine);
            chartLine.render();

            // --- Peta Mini ---
            var miniMap = L.map('miniMap').setView([-7.634317316995929, 110.74809228068428],
                16); // Pusat di Jelobo, zoom out sedikit

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(miniMap);

            miniMap.invalidateSize(); // Penting untuk rendering peta di tab/modal

            var roadsDataMiniMap = {!! json_encode($roadsGeoJsonForMiniMap) !!};

            // Fungsi untuk mendapatkan warna berdasarkan prioritas atau kondisi awal (sama seperti map.blade.php)
            function getColorForPath(properties) { // Nama fungsi disamakan: getColorForPath
                if (properties.prioritas_klasifikasi && properties.prioritas_klasifikasi !== 'tidak ada' &&
                    properties.prioritas_klasifikasi !== 'belum diklasifikasi') {
                    switch (properties.prioritas_klasifikasi) {
                        case 'tinggi':
                            return 'red';
                        case 'sedang':
                            return 'orange';
                        case 'rendah':
                            return 'green';
                        default:
                            return 'gray';
                    }
                } else if (properties.kondisi_awal) {
                    switch (properties.kondisi_awal) {
                        case 'rusak berat':
                            return 'red';
                        case 'rusak sedang':
                            return 'orange';
                        case 'rusak ringan':
                            return 'yellow';
                            break; // break ini tidak diperlukan setelah return
                        case 'baik':
                            return 'green';
                            break; // break ini tidak diperlukan setelah return
                        default:
                            return 'blue';
                            break; // break ini tidak diperlukan setelah return
                    }
                }
                return 'blue';
            }

            // Fungsi untuk membuat pop-up content (sama persis seperti map.blade.php)
            function createPopupContent(properties) { // Nama fungsi disamakan: createPopupContent
                let content = `<div class="info-box">`;
                content += `<h5>${properties.nama_jalan}</h5>`;
                content += `<p><strong>Panjang:</strong> ${properties.panjang_jalan} m</p>`;
                content += `<p><strong>Kondisi Awal:</strong> ${properties.kondisi_awal}</p>`;
                content += `<p><strong>Regional:</strong> RT:  ${properties.regional}`;

                // Tambahkan RW dan Dusun jika ada
                if (properties.rw_regional && properties.rw_regional !== 'N/A') {
                    content += `, RW: ${properties.rw_regional}`;
                }
                if (properties.dusun_regional && properties.dusun_regional !== 'N/A') {
                    content += `, Dusun: ${properties.dusun_regional}`;
                }
                content += `</p>`;


                if (properties.laporan_kerusakan && properties.laporan_kerusakan.length > 0) {
                    content += `<h6>Laporan Kerusakan Terbaru:</h6>`;
                    // Tampilkan hanya laporan terbaru
                    const latestReport = properties.laporan_kerusakan[
                        0]; // Karena sudah diurutkan descending di controller

                    let statusText = latestReport.status_perbaikan;
                    let statusClass = '';
                    if (statusText === 'belum diperbaiki') statusClass = 'status-belum';
                    else if (statusText === 'dalam perbaikan') statusClass = 'status-dalam';
                    else if (statusText === 'sudah diperbaiki') statusClass = 'status-sudah';

                    // Ambil prioritas jalan dari properti jalan, bukan dari laporan kerusakan
                    let priorityTextJalan = properties.prioritas_klasifikasi;
                    let priorityClassJalan = '';
                    if (priorityTextJalan === 'tinggi') priorityClassJalan = 'priority-high';
                    else if (priorityTextJalan === 'sedang') priorityClassJalan = 'priority-medium';
                    else if (priorityTextJalan === 'rendah') priorityClassJalan = 'priority-low';
                    else if (priorityTextJalan === 'belum diklasifikasi') priorityClassJalan =
                        'priority-unclassified';


                    content += `<div class="mb-2 nested-info-box">`;
                    content += `<p><strong>Tanggal Lapor:</strong> ${latestReport.tanggal_lapor}</p>`;
                    content += `<p><strong>Tingkat Kerusakan:</strong> ${latestReport.tingkat_kerusakan}</p>`;
                    if (latestReport.tingkat_lalu_lintas) {
                        content +=
                            `<p><strong>Tingkat Lalu Lintas:</strong> ${latestReport.tingkat_lalu_lintas}</p>`;
                    }
                    // Prioritas jalan sekarang diletakkan di sini
                    content +=
                        `<p><strong>Prioritas Jalan:</strong> <span class="priority-badge ${priorityClassJalan}">${priorityTextJalan.toUpperCase()}</span></p>`;
                    content +=
                        `<p><strong>Status Perbaikan:</strong> <span class="status-badge ${statusClass}">${statusText.toUpperCase()}</span></p>`;
                    content += `</div>`;

                    if (properties.laporan_kerusakan.length > 1) {
                        content += `<small>(${properties.laporan_kerusakan.length - 1} laporan lain)</small>`;
                    }
                    // Link ke halaman show laporan kerusakan untuk laporan terbaru
                    content +=
                        `<p class="mt-3"><a href="{{ route('kerusakan-jalan.show', '') }}/${latestReport.id}">Lihat Detail Laporan Terbaru</a></p>`;

                } else {
                    content += `<p>Tidak ada laporan kerusakan jalan.</p>`;
                    // Jika tidak ada laporan, tetap tampilkan prioritas jalan
                    let priorityTextJalan = properties.prioritas_klasifikasi;
                    let priorityClassJalan = '';
                    if (priorityTextJalan === 'tinggi') priorityClassJalan = 'priority-high';
                    else if (priorityTextJalan === 'sedang') priorityClassJalan = 'priority-medium';
                    else if (priorityTextJalan === 'rendah') priorityClassJalan = 'priority-low';
                    else if (priorityTextJalan === 'belum diklasifikasi') priorityClassJalan =
                        'priority-unclassified';

                    content +=
                        `<p><strong>Prioritas Jalan:</strong> <span class="priority-badge ${priorityClassJalan}">${priorityTextJalan.toUpperCase()}</span></p>`;
                }
                return content;
            }


            L.geoJSON(roadsDataMiniMap, {
                style: function(feature) {
                    return {
                        color: getColorForPath(feature
                            .properties), // <-- Pastikan ini memanggil getColorForPath
                        weight: 3, // Berat garis lebih kecil untuk mini map
                        opacity: 0.7
                    };
                },
                onEachFeature: function(feature, layer) {
                    if (feature.properties && feature.geometry && feature.geometry.coordinates &&
                        feature.geometry.coordinates.length > 0) {
                        layer.bindPopup(createPopupContent(feature
                            .properties), { // <-- Pastikan ini memanggil createPopupContent
                            maxWidth: 300 // Max width sama
                        });
                    }
                }
            }).addTo(miniMap);
        });
    </script>
@endpush
