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
                    <label for="jenis_jalan" class="form-label">Jenis Jalan</label>
                    <select class="form-select" id="jenis_jalan" name="jenis_jalan" required>
                        <option value="">Pilih Jenis Jalan</option>
                        <option value="aspal" {{ old('jenis_jalan', $jalan->jenis_jalan) == 'aspal' ? 'selected' : '' }}>
                            Aspal</option>
                        <option value="beton" {{ old('jenis_jalan', $jalan->jenis_jalan) == 'beton' ? 'selected' : '' }}>
                            Beton</option>
                        <option value="paving" {{ old('jenis_jalan', $jalan->jenis_jalan) == 'paving' ? 'selected' : '' }}>
                            Paving</option>
                        <option value="tanah" {{ old('jenis_jalan', $jalan->jenis_jalan) == 'tanah' ? 'selected' : '' }}>
                            Tanah</option>
                    </select>
                </div>

                {{-- (Sisa form tidak berubah) --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="regional_id" class="form-label">Regional RT</label>
                        <select class="form-select" id="regional_id" name="regional_id" required>
                            <option value="">Pilih RT</option>
                            @foreach ($regionals->where('tipe_regional', 'RT') as $regional)
                                <option value="{{ $regional->id }}"
                                    {{ $jalan->regional_id == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nama_regional }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rw_regional_id" class="form-label">Regional RW</label>
                        <select class="form-select" id="rw_regional_id" name="rw_regional_id" required>
                            <option value="">Pilih RW</option>
                            @foreach ($regionals->where('tipe_regional', 'RW') as $regional)
                                <option value="{{ $regional->id }}"
                                    {{ $jalan->rw_regional_id == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nama_regional }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="dusun_regional_id" class="form-label">Regional Dusun</label>
                        <select class="form-select" id="dusun_regional_id" name="dusun_regional_id" required>
                            <option value="">Pilih Dusun</option>
                            @foreach ($regionals->where('tipe_regional', 'Dusun') as $regional)
                                <option value="{{ $regional->id }}"
                                    {{ $jalan->dusun_regional_id == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nama_regional }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Akhir Bagian Dropdown Regional --}}

                <div class="mb-3">
                    <label class="form-label">Gambar Garis Jalan (Peta)</label>
                    <div id="mapid"></div>
                    <input type="hidden" name="geometri_json" id="geometri_json"
                        value="{{ old('geometri_json', $existingGeomCoords) }}">
                    @error('geometri_json')
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

            map.invalidateSize();

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

                    var existingGeomCoords = JSON.parse(document.getElementById('geometri_json').value);
                    if (existingGeomCoords && existingGeomCoords.length > 0) {
                        var polyline = L.polyline(existingGeomCoords, {
                            color: 'red'
                        }).addTo(drawnItems);
                        map.fitBounds(polyline.getBounds());
                    } else {
                        map.setView([-7.701469, 110.746014], 16);
                    }

                    map.on(L.Draw.Event.CREATED, function(event) {
                        var layer = event.layer;
                        drawnItems.clearLayers();
                        drawnItems.addLayer(layer);
                        var latlngs = layer.getLatLngs();
                        var coords = latlngs.map(function(latlng) {
                            return [latlng.lat, latlng.lng];
                        });
                        document.getElementById('geometri_json').value = JSON.stringify(coords);
                    });

                    map.on(L.Draw.Event.EDITED, function(event) {
                        event.layers.eachLayer(function(layer) {
                            if (layer instanceof L.Polyline) {
                                var latlngs = layer.getLatLngs();
                                var coords = latlngs.map(function(latlng) {
                                    return [latlng.lat, latlng.lng];
                                });
                                document.getElementById('geometri_json').value = JSON.stringify(
                                    coords);
                            }
                        });
                    });

                    map.on(L.Draw.Event.DELETED, function(event) {
                        document.getElementById('geometri_json').value = '[]';
                    });
                } else {
                    console.warn("L.Control.Draw belum terdefinisi. Mencoba lagi dalam 100ms...");
                    setTimeout(initializeDrawControl, 100);
                }
            }

            initializeDrawControl();

            // Hapus JavaScript untuk menonaktifkan dropdown lain, karena sekarang semua wajib diisi
        });
    </script>
@endpush
