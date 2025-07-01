{{-- resources/views/dashboard/map.blade.php --}}
@extends('layouts.app')

@section('title', 'Peta Overview Jalan & Kerusakan')

@push('styles')
    <style>
        /* Tambahan: Fixed Header */
        .navbar-header {
            position: fixed;
            top: 0;
            left: 250px;
            /* Sesuaikan jika sidebar bukan 250px */
            width: calc(100% - 250px);
            z-index: 1050;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }

        body {
            padding-top: 80px;
            /* Agar konten tidak ketiban header */
            overflow-y: auto;
        }

        #mapid {
            height: 700px;
            width: 100%;
            border-radius: 8px;
        }

        .leaflet-container {
            background: #fff;
        }

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

        .info-box .foto-kerusakan {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Peta Overview Jalan & Kerusakan</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Peta Overview</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            {{-- Filter Form --}}
            <form id="mapFilterForm" class="mb-4 d-flex flex-wrap align-items-end gap-3">

                <div class="flex-grow-1">
                    <label for="filter_prioritas" class="form-label text-sm">Prioritas</label>
                    <select class="form-select form-select-sm" id="filter_prioritas" name="prioritas">
                        <option value="">Semua Prioritas</option>
                        <option value="tinggi" {{ $filterPrioritas === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="sedang" {{ $filterPrioritas === 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="rendah" {{ $filterPrioritas === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="belum_diklasifikasi"
                            {{ $filterPrioritas === 'belum_diklasifikasi' ? 'selected' : '' }}>Belum Diklasifikasi</option>
                    </select>
                </div>

                <div class="flex-grow-1">
                    <label for="filter_status" class="form-label text-sm">Status Perbaikan</label>
                    <select class="form-select form-select-sm" id="filter_status" name="status_perbaikan">
                        <option value="">Semua Status</option>
                        <option value="belum_diperbaiki"
                            {{ $filterStatusPerbaikan === 'belum_diperbaiki' ? 'selected' : '' }}>Belum Diperbaiki</option>
                        <option value="dalam_perbaikan"
                            {{ $filterStatusPerbaikan === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="sudah_diperbaiki"
                            {{ $filterStatusPerbaikan === 'sudah_diperbaiki' ? 'selected' : '' }}>Sudah Diperbaiki</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary btn-sm">Filter Peta</button>
                    <a href="{{ route('map.overview') }}" class="btn btn-secondary btn-sm ms-2">Reset Filter</a>
                </div>
            </form>

            <div id="mapid"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var map = L.map('mapid'); // Inisialisasi peta tanpa setView awal

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            map.invalidateSize(); // Memastikan peta dirender dengan benar setelah elemen DOM siap

            var roadsData = {!! json_encode($roadsGeoJson) !!}; // Data jalan dari controller

            console.log("Roads GeoJSON Data for Map:", roadsData); // Debugging: lihat data di konsol

            // Fungsi untuk mendapatkan warna berdasarkan prioritas atau kondisi awal
            function getColorForPath(properties) {
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

            // Fungsi untuk membuat pop-up content
            function createPopupContent(properties) {
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
                        if (laporan.foto_url) {
                            content +=
                                `<p><strong>Foto:</strong></p><img src="${laporan.foto_url}" class="foto-kerusakan" alt="Foto Kerusakan">`;
                        }
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
                return content;
            }

            L.geoJSON(roadsData, {
                style: function(feature) {
                    return {
                        color: getColorForPath(feature.properties),
                        weight: 5,
                        opacity: 0.7
                    };
                },
                onEachFeature: function(feature, layer) {
                    if (feature.properties && feature.geometry && feature.geometry.coordinates &&
                        feature.geometry.coordinates.length > 0) {
                        layer.bindPopup(createPopupContent(feature.properties), {
                            maxWidth: 300
                        });
                    }
                }
            }).addTo(map);

            // Jika tidak ada data jalan yang ditampilkan setelah filter, set view ke Desa Jelobo
            if (roadsData.length === 0) {
                map.setView([-7.634317316995929, 110.74809228068428],
                    16); // Pusat di Kantor Desa Jelobo, zoom out sedikit
            } else {
                // Jika ada data, sesuaikan tampilan peta agar semua fitur terlihat
                try {
                    var allFeatures = L.geoJSON(roadsData);
                    map.fitBounds(allFeatures.getBounds());
                } catch (e) {
                    console.error("Error fitting map bounds to all features:", e);
                    map.setView([-7.634317316995929, 110.74809228068428],
                        16); // Fallback ke Jelobo jika ada error
                }
            }
        });
    </script>
@endpush
