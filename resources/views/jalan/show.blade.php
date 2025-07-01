{{-- resources/views/jalan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Data Jalan: ' . $jalan->nama_jalan)

@push('styles')
    <style>
        #mapid {
            height: 400px;
            width: 100%;
            border-radius: 8px;
        }

        .leaflet-container {
            background: #fff;
        }
    </style>
    {{-- Leaflet CSS dimuat di layouts/app.blade.php dari CDN --}}
@endpush

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Detail Data Jalan</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">
                <a href="{{ route('jalan.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    Data Jalan
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Detail</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            <h5 class="card-title mb-4">Informasi Jalan</h5>
            <dl class="row mb-4">
                <dt class="col-sm-3">Nama Jalan:</dt>
                <dd class="col-sm-9">{{ $jalan->nama_jalan }}</dd>

                <dt class="col-sm-3">Panjang Jalan:</dt>
                <dd class="col-sm-9">{{ $jalan->panjang_jalan }} meter</dd>

                <dt class="col-sm-3">Kondisi Awal:</dt>
                <dd class="col-sm-9">{{ $jalan->kondisi_jalan }}</dd>

                <dt class="col-sm-3">Regional RT:</dt>
                <dd class="col-sm-9">{{ $jalan->regional->nama_regional ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Regional RW:</dt>
                <dd class="col-sm-9">{{ $jalan->rwRegional->nama_regional ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Regional Dusun:</dt>
                <dd class="col-sm-9">{{ $jalan->dusunRegional->nama_regional ?? 'N/A' }}</dd>
            </dl>

            <h5 class="card-title mb-4">Visualisasi Geometri</h5>
            <div id="mapid" class="mb-4"></div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('jalan.edit', $jalan->id) }}" class="btn btn-warning me-2">Edit</a>
                <a href="{{ route('jalan.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var map = L.map('mapid');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            map.invalidateSize(); // Memastikan peta dirender dengan benar

            var jalanGeometri = {!! json_encode($jalan->geometri_json) !!}; // Ambil objek GeoJSON LineString lengkap dari PHP

            if (jalanGeometri && jalanGeometri.type === 'LineString' && jalanGeometri.coordinates && jalanGeometri
                .coordinates.length > 0) {
                try {
                    var geojsonLayer = L.geoJSON(jalanGeometri, {
                        style: function(feature) {
                            return {
                                color: 'blue',
                                weight: 4
                            };
                        }
                    }).addTo(map);

                    map.fitBounds(geojsonLayer.getBounds());
                } catch (e) {
                    console.error("Error saat menambahkan GeoJSON layer di Detail Jalan:", e);
                    map.setView([-7.701469, 110.746014], 16);
                }
            } else {
                map.setView([-7.701469, 110.746014], 16);
            }
        });
    </script>
@endpush
