{{-- resources/views/jalan/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Data Jalan: ' . $jalan->nama_jalan)

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
    {{-- Leaflet CSS dan Leaflet Draw CSS dimuat di layouts/app.blade.php dari CDN --}}
@endpush

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Edit Data Jalan</h6>
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
            <li class="fw-medium">Edit</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            <form action="{{ route('jalan.update', $jalan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nama_jalan" class="form-label">Nama Jalan</label>
                    <input type="text" class="form-control @error('nama_jalan') is-invalid @enderror" id="nama_jalan"
                        name="nama_jalan" value="{{ old('nama_jalan', $jalan->nama_jalan) }}" required>
                    @error('nama_jalan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="panjang_jalan" class="form-label">Panjang Jalan (meter)</label>
                    <input type="number" step="0.01" class="form-control @error('panjang_jalan') is-invalid @enderror"
                        id="panjang_jalan" name="panjang_jalan" value="{{ old('panjang_jalan', $jalan->panjang_jalan) }}"
                        required>
                    @error('panjang_jalan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="kondisi_jalan" class="form-label">Kondisi Awal Jalan</label>
                    <select class="form-select @error('kondisi_jalan') is-invalid @enderror" id="kondisi_jalan"
                        name="kondisi_jalan" required>
                        <option value="">Pilih Kondisi</option>
                        <option value="baik"
                            {{ old('kondisi_jalan', $jalan->kondisi_jalan) == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak ringan" {{ old('kondisi_jalan') == 'rusak ringan' ? 'selected' : '' }}>Rusak
                            Ringan</option>
                        <option value="rusak sedang" {{ old('kondisi_jalan') == 'rusak sedang' ? 'selected' : '' }}>Rusak
                            Sedang</option>
                        <option value="rusak berat" {{ old('kondisi_jalan') == 'rusak berat' ? 'selected' : '' }}>Rusak
                            Berat</option>
                    </select>
                    @error('kondisi_jalan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="regional_id" class="form-label">Regional</label>
                    <select class="form-select @error('regional_id') is-invalid @enderror" id="regional_id"
                        name="regional_id" required>
                        <option value="">Pilih Regional</option>
                        @foreach ($regionals as $regional)
                            <option value="{{ $regional->id }}"
                                {{ old('regional_id', $jalan->regional_id) == $regional->id ? 'selected' : '' }}>
                                {{ $regional->nama_regional }} ({{ $regional->tipe_regional }})
                            </option>
                        @endforeach
                    </select>
                    @error('regional_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Garis Jalan (Peta)</label>
                    <div id="mapid"></div>
                    <input type="hidden" name="geometri_coords" id="geometri_coords"
                        value="{{ old('geometri_coords', $existingGeomCoords) }}">
                    @error('geometri_coords')
                        <div class="text-danger mt-2">Peta: {{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Gambar ulang garis jalan atau edit yang sudah ada. Klik dua kali
                        untuk mengakhiri garis baru.</small>
                </div>

                <button type="submit" class="btn btn-primary">Update Jalan</button>
                <a href="{{ route('jalan.index') }}" class="btn btn-secondary">Batal</a>
            </form>
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

            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            function initializeDrawControl() {
                if (typeof L.Control.Draw !== 'undefined') {
                    var drawControl = new L.Control.Draw({
                        edit: {
                            featureGroup: drawnItems,
                            poly: {
                                allowIntersection: false
                            }
                        },
                        draw: {
                            polygon: false,
                            marker: false,
                            circlemarker: false,
                            circle: false,
                            rectangle: false,
                            polyline: {
                                shapeOptions: {
                                    color: '#f30'
                                },
                                metric: true
                            }
                        }
                    });
                    map.addControl(drawControl);

                    var existingGeomCoords = JSON.parse(document.getElementById('geometri_coords').value);
                    if (existingGeomCoords && existingGeomCoords.length > 0) {
                        var polyline = L.polyline(existingGeomCoords, {
                            color: 'red'
                        }).addTo(drawnItems);
                        map.fitBounds(polyline.getBounds()); // Sesuaikan tampilan peta agar garis terlihat
                    } else {
                        // Jika tidak ada garis yang ada, set view ke Kantor Desa Jelobo
                        map.setView([-7.701469, 110.746014], 16); // Lintang, Bujur, Zoom Level
                    }

                    // Event listeners
                    map.on(L.Draw.Event.CREATED, function(event) {
                        var layer = event.layer;
                        drawnItems.clearLayers();
                        drawnItems.addLayer(layer);
                        var latlngs = layer.getLatLngs();
                        var coords = latlngs.map(function(latlng) {
                            return [latlng.lat, latlng.lng];
                        });
                        document.getElementById('geometri_coords').value = JSON.stringify(coords);
                    });

                    map.on(L.Draw.Event.EDITED, function(event) {
                        event.layers.eachLayer(function(layer) {
                            if (layer instanceof L.Polyline) {
                                var latlngs = layer.getLatLngs();
                                var coords = latlngs.map(function(latlng) {
                                    return [latlng.lat, latlng.lng];
                                });
                                document.getElementById('geometri_coords').value = JSON.stringify(
                                    coords);
                            }
                        });
                    });

                    map.on(L.Draw.Event.DELETED, function(event) {
                        document.getElementById('geometri_coords').value = '[]';
                    });
                } else {
                    console.warn("L.Control.Draw belum terdefinisi. Mencoba lagi dalam 100ms...");
                    setTimeout(initializeDrawControl, 100);
                }
            }

            initializeDrawControl();
        });
    </script>
@endpush
