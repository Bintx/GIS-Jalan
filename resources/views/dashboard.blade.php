@extends('layouts.app')

@section('title', 'Dashboard - Sistem Informasi Perbaikan Jalan')

@section('content')
    {{-- Tambahkan gaya CSS di sini jika Anda ingin header tetap terlihat saat menggulir.
         Anda perlu mengidentifikasi elemen header di layouts/app.blade.php
         dan menerapkan gaya seperti 'position: fixed; top: 0; width: 100%; z-index: 1000;'
         Contoh:
         <style>
             .main-header { /* Ganti .main-header dengan kelas atau ID header Anda */
                 position: fixed;
                 top: 0;
                 width: 100%;
                 z-index: 1000; /* Pastikan z-index cukup tinggi */
                 background-color: white; /* Tambahkan warna latar belakang agar tidak transparan */
                 box-shadow: 0 2px 4px rgba(0,0,0,.1); /* Opsional: tambahkan bayangan */
             }
             body {
                 padding-top: 70px; /* Sesuaikan dengan tinggi header Anda untuk mencegah konten tertutup */
             }
         </style>
    --}}

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
                    max: 10,
                    tickAmount: 10
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

            var roadsGeoJsonForMiniMap = {!! json_encode($roadsGeoJsonForMiniMap) !!};

            // Fungsi untuk mendapatkan warna berdasarkan prioritas atau kondisi awal (sama seperti di mapOverview)
            function getColorForMiniMapPath(properties) {
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
                        case 'baik':
                            return 'green';
                        default:
                            return 'blue';
                    }
                }
                return 'blue';
            }

            // Fungsi untuk membuat pop-up content (sama seperti di mapOverview)
            function createMiniMapPopupContent(properties) {
                let content = `<div class="info-box">`;
                content += `<h5>${properties.nama_jalan}</h5>`;
                content += `<p><strong>Panjang:</strong> ${properties.panjang_jalan} m</p>`;
                content += `<p><strong>Kondisi Awal:</strong> ${properties.kondisi_awal}</p>`;
                content += `<p><strong>Regional:</strong> ${properties.regional} (${properties.regional_tipe})</p>`;

                let priorityText = properties.prioritas_klasifikasi;
                let priorityClass = '';
                if (priorityText === 'tinggi') priorityClass = 'priority-high';
                else if (priorityText === 'sedang') priorityClass = 'priority-medium';
                else if (priorityText === 'rendah') priorityClass = 'priority-low';
                else if (priorityText === 'belum diklasifikasi') priorityClass = 'priority-unclassified';

                content +=
                    `<p><strong>Prioritas:</strong> <span class="priority-badge ${priorityClass}">${priorityText.toUpperCase()}</span></p>`;

                if (properties.laporan_kerusakan && properties.laporan_kerusakan.length > 0) {
                    content += `<h6>Laporan Kerusakan Terbaru:</h6>`;
                    const latestReports = properties.laporan_kerusakan.slice(0, 1);
                    latestReports.forEach(laporan => {
                        let statusText = laporan.status_perbaikan;
                        let statusClass = '';
                        if (statusText === 'belum diperbaiki') statusClass = 'status-belum';
                        else if (statusText === 'dalam perbaikan') statusClass = 'status-dalam';
                        else if (statusText === 'sudah diperbaiki') statusClass = 'status-sudah';

                        content += `<div class="mb-2 nested-info-box">`;
                        content += `<p><strong>Tanggal:</strong> ${laporan.tanggal_lapor}</p>`;
                        content += `<p><strong>Tingkat:</strong> ${laporan.tingkat_kerusakan}</p>`;
                        content +=
                            `<p><strong>Prioritas Laporan:</strong> ${laporan.prioritas ? `<span class="priority-badge ${priorityClass}">${laporan.prioritas.toUpperCase()}</span>` : 'Belum Diklasifikasi'}</p>`;
                        content +=
                            `<p><strong>Status:</strong> <span class="status-badge ${statusClass}">${statusText.toUpperCase()}</span></p>`;
                        if (laporan.deskripsi) content +=
                            `<p><strong>Deskripsi:</strong> ${laporan.deskripsi}</p>`;
                        // Foto tidak ditampilkan di pop-up mini map
                        content += `</div>`;
                    });
                    if (properties.laporan_kerusakan.length > 1) {
                        content += `<small>(${properties.laporan_kerusakan.length - 1} laporan lain)</small>`;
                    }
                    content +=
                        `<p><a href="{{ route('kerusakan-jalan.index') }}?jalan_id=${properties.id}" target="_blank">Lihat Semua Laporan Jalan Ini</a></p>`;

                } else {
                    content += `<p>Tidak ada laporan kerusakan jalan.</p>`;
                }
                return `<div class="info-box">${content}</div>`;
            }


            if (roadsGeoJsonForMiniMap && roadsGeoJsonForMiniMap.length > 0) {
                L.geoJSON(roadsGeoJsonForMiniMap, {
                    style: function(feature) {
                        return {
                            color: getColorForMiniMapPath(feature.properties),
                            weight: 3,
                            opacity: 0.7
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.geometry && feature.geometry.coordinates &&
                            feature.geometry.coordinates.length > 0) {
                            layer.bindPopup(createMiniMapPopupContent(feature.properties), {
                                maxWidth: 300
                            });
                        }
                    }
                }).addTo(miniMap);
            }
        });
    </script>
@endpush
