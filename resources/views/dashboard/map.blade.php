{{-- resources/views/dashboard/map.blade.php --}}
@extends('layouts.app')

@section('title', 'Peta Overview Jalan & Kerusakan')

@push('styles')
    <style>
        #mapid {
            height: 700px;
            /* Tinggi peta yang lebih besar untuk overview */
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
        }

        .info-box .priority-high {
            background-color: #dc3545;
        }

        /* Merah */
        .info-box .priority-medium {
            background-color: #ffc107;
            color: #212529;
        }

        /* Kuning */
        .info-box .priority-low {
            background-color: #28a745;
        }

        /* Hijau */
        .info-box .priority-unclassified {
            background-color: #6c757d;
        }

        /* Abu-abu */

        .info-box .status-badge {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            color: white;
            display: inline-block;
            margin-bottom: 5px;
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
            <div id="mapid"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var map = L.map('mapid').setView([-7.634317316995929, 110.74809228068428],
                16); // Pusat di Kantor Desa Jelobo, zoom out sedikit untuk overview

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            map.invalidateSize(); // Memastikan peta dirender dengan benar setelah elemen DOM siap

            var roadsData = {!! json_encode($roadsGeoJson) !!}; // Data jalan dari controller

            console.log("Roads GeoJSON Data for Map:", roadsData); // Debugging: lihat data di konsol

            // Fungsi untuk mendapatkan warna berdasarkan prioritas
            function getColorForPriority(priority) {
                switch (priority) {
                    case 'tinggi':
                        return 'red';
                    case 'sedang':
                        return 'orange';
                    case 'rendah':
                        return 'green';
                    case 'belum diklasifikasi':
                        return 'gray';
                    default:
                        return 'blue'; // Default untuk kondisi baik atau tidak ada laporan
                }
            }

            // Fungsi untuk membuat pop-up content
            function createPopupContent(properties) {
                let content = `<h5>${properties.nama_jalan}</h5>`;
                content += `<p><strong>Panjang:</strong> ${properties.panjang_jalan} m</p>`;
                content += `<p><strong>Kondisi Awal:</strong> ${properties.kondisi_awal}</p>`;
                content += `<p><strong>Regional:</strong> ${properties.regional} (${properties.regional_tipe})</p>`;

                let priorityClass = '';
                if (properties.prioritas_klasifikasi === 'tinggi') priorityClass = 'priority-high';
                else if (properties.prioritas_klasifikasi === 'sedang') priorityClass = 'priority-medium';
                else if (properties.prioritas_klasifikasi === 'rendah') priorityClass = 'priority-low';
                else if (properties.prioritas_klasifikasi === 'belum diklasifikasi') priorityClass =
                    'priority-unclassified';

                content +=
                    `<p><strong>Prioritas:</strong> <span class="priority-badge ${priorityClass}">${properties.prioritas_klasifikasi.toUpperCase()}</span></p>`;

                if (properties.laporan_kerusakan && properties.laporan_kerusakan.length > 0) {
                    content += `<h6>Laporan Kerusakan (${properties.laporan_kerusakan.length} Laporan):</h6>`;
                    properties.laporan_kerusakan.forEach(laporan => {
                        let statusClass = '';
                        if (laporan.status_perbaikan === 'belum diperbaiki') statusClass = 'status-belum';
                        else if (laporan.status_perbaikan === 'dalam perbaikan') statusClass =
                            'status-dalam';
                        else if (laporan.status_perbaikan === 'sudah diperbaiki') statusClass =
                            'status-sudah';

                        content += `<div class="mb-2 info-box">`; // Nested info-box for reports
                        content += `<p><strong>Tanggal:</strong> ${laporan.tanggal_lapor}</p>`;
                        content += `<p><strong>Tingkat:</strong> ${laporan.tingkat_kerusakan}</p>`;
                        content +=
                            `<p><strong>Prioritas Laporan:</strong> ${laporan.prioritas ? `<span class="priority-badge ${priorityClass}">${laporan.prioritas.toUpperCase()}</span>` : 'Belum Diklasifikasi'}</p>`;
                        content +=
                            `<p><strong>Status:</strong> <span class="status-badge ${statusClass}">${laporan.status_perbaikan.toUpperCase()}</span></p>`;
                        if (laporan.deskripsi) content +=
                            `<p><strong>Deskripsi:</strong> ${laporan.deskripsi}</p>`;
                        if (laporan.foto_url) {
                            content +=
                                `<p><strong>Foto:</strong></p><img src="${laporan.foto_url}" class="foto-kerusakan" alt="Foto Kerusakan">`;
                        }
                        content += `</div>`;
                    });
                } else {
                    content += `<p>Tidak ada laporan kerusakan jalan.</p>`;
                }
                return `<div class="info-box">${content}</div>`;
            }

            L.geoJSON(roadsData, {
                style: function(feature) {
                    return {
                        color: getColorForPriority(feature.properties.prioritas_klasifikasi),
                        weight: 5,
                        opacity: 0.7
                    };
                },
                onEachFeature: function(feature, layer) {
                    if (feature.properties) {
                        layer.bindPopup(createPopupContent(feature.properties));
                    }
                }
            }).addTo(map);

            // Opsional: Atur ulang tampilan peta agar semua fitur terlihat jika diinginkan
            // if (roadsData.length > 0) {
            //     var bounds = L.featureGroup(
            //         roadsData.map(function(feature) {
            //             return L.geoJSON(feature);
            //         })
            //     ).getBounds();
            //     map.fitBounds(bounds);
            // }
        });
    </script>
@endpush
