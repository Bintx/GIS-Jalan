@extends('layouts.app')

@section('title', 'Tambah Data Jalan Baru')

@section('content')
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title">Formulir Tambah Data Jalan</h5>
            <form action="{{ route('jalan.store') }}" method="POST" id="jalanForm">
                @csrf
                <div class="mb-3">
                    <label for="nama_jalan" class="form-label">Nama Jalan</label>
                    <input type="text" class="form-control" id="nama_jalan" name="nama_jalan"
                        value="{{ old('nama_jalan') }}" required>
                </div>
                <div class="mb-3">
                    <label for="panjang_jalan" class="form-label">Panjang Jalan (meter)</label>
                    <input type="number" class="form-control" id="panjang_jalan" name="panjang_jalan"
                        value="{{ old('panjang_jalan') }}" required>
                </div>

                {{-- REVISI: Mengganti input 'Kondisi' menjadi 'Jenis Jalan' --}}
                <div class="mb-3">
                    <label for="jenis_jalan" class="form-label">Jenis Jalan</label>
                    <select class="form-select" id="jenis_jalan" name="jenis_jalan" required>
                        <option value="">Pilih Jenis Jalan</option>
                        <option value="aspal" {{ old('jenis_jalan') == 'aspal' ? 'selected' : '' }}>Aspal</option>
                        <option value="beton" {{ old('jenis_jalan') == 'beton' ? 'selected' : '' }}>Beton</option>
                        <option value="paving" {{ old('jenis_jalan') == 'paving' ? 'selected' : '' }}>Paving</option>
                        <option value="tanah" {{ old('jenis_jalan') == 'tanah' ? 'selected' : '' }}>Tanah</option>
                    </select>
                </div>

                {{-- (Sisa form tidak berubah) --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="regional_id" class="form-label">Regional RT</label>
                        <select class="form-select" id="regional_id" name="regional_id" required>
                            <option value="">Pilih RT</option>
                            @foreach ($regionals->where('tipe_regional', 'RT') as $regional)
                                <option value="{{ $regional->id }}">{{ $regional->nama_regional }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rw_regional_id" class="form-label">Regional RW</label>
                        <select class="form-select" id="rw_regional_id" name="rw_regional_id" required>
                            <option value="">Pilih RW</option>
                            @foreach ($regionals->where('tipe_regional', 'RW') as $regional)
                                <option value="{{ $regional->id }}">{{ $regional->nama_regional }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="dusun_regional_id" class="form-label">Regional Dusun</label>
                        <select class="form-select" id="dusun_regional_id" name="dusun_regional_id" required>
                            <option value="">Pilih Dusun</option>
                            @foreach ($regionals->where('tipe_regional', 'Dusun') as $regional)
                                <option value="{{ $regional->id }}">{{ $regional->nama_regional }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Peta Jalan</label>
                    <div id="map" style="height: 400px;"></div>
                    <input type="hidden" id="geometri_json" name="geometri_json">
                    @error('geometri_json')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('jalan.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- (Script untuk Leaflet Draw tidak berubah) --}}
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script>
        $(document).ready(function() {
            // var map = L.map('map').setView([-7.701469, 110.746014], 15);
            var map = L.map('map').setView([-7.634317316995929, 110.74809228068428],
                16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);
            var drawControl = new L.Control.Draw({
                edit: {
                    featureGroup: drawnItems
                },
                draw: {
                    polygon: false,
                    marker: false,
                    circle: false,
                    circlemarker: false,
                    rectangle: false,
                    polyline: {
                        shapeOptions: {
                            color: '#f00'
                        }
                    }
                }
            });
            map.addControl(drawControl);
            map.on(L.Draw.Event.CREATED, function(event) {
                var layer = event.layer;
                drawnItems.clearLayers();
                drawnItems.addLayer(layer);
                var geojson = layer.toGeoJSON();
                $('#geometri_json').val(JSON.stringify(geojson.geometry));
            });
            $('#jalanForm').submit(function() {
                if ($('#geometri_json').val() === '') {
                    alert('Harap gambar rute jalan di peta terlebih dahulu.');
                    return false;
                }
            });
        });
    </script>
@endpush
