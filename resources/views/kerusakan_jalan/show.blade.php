{{-- resources/views/kerusakan_jalan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Laporan Kerusakan: ' . ($kerusakanJalan->jalan->nama_jalan ?? 'N/A'))

@push('styles')
    <style>
        #mapid {
            height: 400px;
            /* Tinggi peta */
            width: 100%;
            /* Lebar peta */
            border-radius: 8px;
            /* Sudut melengkung */
            margin-top: 20px;
            /* Jarak dari konten di atasnya */
            margin-bottom: 20px;
            /* Jarak dari konten di bawahnya */
        }

        .leaflet-container {
            background: #fff;
            /* Pastikan background peta cerah */
        }
    </style>
    {{-- Leaflet CSS sudah dimuat di layouts/app.blade.php dari CDN --}}
@endpush

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Detail Laporan Kerusakan Jalan</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">
                <a href="{{ route('kerusakan-jalan.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    Laporan Kerusakan
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Detail</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            <h5 class="card-title mb-4">Informasi Laporan Kerusakan</h5>
            <dl class="row mb-4">
                <dt class="col-sm-4">Nama Jalan:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->jalan->nama_jalan ?? 'Jalan Tidak Ditemukan' }}</dd>

                {{-- Tampilkan Regional RT, RW, Dusun secara terpisah --}}
                <dt class="col-sm-4">Regional RT:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->jalan->regional->nama_regional ?? 'N/A' }}</dd>

                <dt class="col-sm-4">Regional RW:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->jalan->rwRegional->nama_regional ?? 'N/A' }}</dd>

                <dt class="col-sm-4">Regional Dusun:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->jalan->dusunRegional->nama_regional ?? 'N/A' }}</dd>
                {{-- Akhir tampilan Regional terpisah --}}

                <dt class="col-sm-4">Tanggal Lapor:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->tanggal_lapor->format('d M Y') }}</dd>

                <dt class="col-sm-4">Pelapor:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->user->name ?? 'N/A' }}</dd>

                <dt class="col-sm-4">Tingkat Kerusakan:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->tingkat_kerusakan }}</dd>

                <dt class="col-sm-4">Tingkat Lalu Lintas:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->tingkat_lalu_lintas }}</dd>

                <dt class="col-sm-4">Panjang Ruas Rusak:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->panjang_ruas_rusak }} meter</dd>

                <dt class="col-sm-4">Deskripsi Kerusakan:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->deskripsi_kerusakan ?? '-' }}</dd>

                <dt class="col-sm-4">Status Perbaikan:</dt>
                <dd class="col-sm-8">
                    @if ($kerusakanJalan->status_perbaikan == 'belum diperbaiki')
                        <span class="badge bg-danger">Belum Diperbaiki</span>
                    @elseif ($kerusakanJalan->status_perbaikan == 'dalam perbaikan')
                        <span class="badge bg-warning">Dalam Perbaikan</span>
                    @elseif ($kerusakanJalan->status_perbaikan == 'sudah diperbaiki')
                        <span class="badge bg-success">Sudah Diperbaiki</span>
                    @endif
                </dd>

                <dt class="col-sm-4">Klasifikasi Prioritas:</dt>
                <dd class="col-sm-8">
                    @if ($kerusakanJalan->klasifikasi_prioritas == 'tinggi')
                        <span class="badge bg-danger">Tinggi</span>
                    @elseif ($kerusakanJalan->klasifikasi_prioritas == 'sedang')
                        <span class="badge bg-warning">Sedang</span>
                    @elseif ($laporan->klasifikasi_prioritas == 'rendah')
                        <span class="badge bg-success">Rendah</span>
                    @else
                        <span class="badge bg-secondary">Belum Diklasifikasi</span>
                    @endif
                </dd>
            </dl>

            @if ($kerusakanJalan->foto_kerusakan)
                <h5 class="card-title mb-3">Foto Kerusakan</h5>
                <div class="mb-4 text-center">
                    <img src="{{ asset('storage/' . $kerusakanJalan->foto_kerusakan) }}" class="img-fluid rounded"
                        alt="Foto Kerusakan" style="max-width: 500px; height: auto;">
                </div>
            @endif

            <h5 class="card-title mb-3">Lokasi Jalan Terkait</h5>
            <div id="mapid"></div> {{-- Elemen peta --}}

            <div class="d-flex justify-content-end mt-4">
                @if (Auth::check() && Auth::user()->isAdmin())
                    <a href="{{ route('kerusakan-jalan.edit', $kerusakanJalan->id) }}" class="btn btn-warning me-2">Edit
                        Laporan</a>
                @endif
                <a href="{{ route('kerusakan-jalan.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var map = L.map('mapid'); // Inisialisasi peta
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            map.invalidateSize(); // Memastikan peta dirender dengan benar

            // Ambil data geometri dari jalan terkait laporan
            var jalanGeometri = {!! json_encode($kerusakanJalan->jalan->geometri_json) !!};

            if (jalanGeometri && jalanGeometri.type === 'LineString' && jalanGeometri.coordinates && jalanGeometri
                .coordinates.length > 0) {
                try {
                    // L.geoJSON sudah bisa memproses objek GeoJSON lengkap langsung
                    var geojsonLayer = L.geoJSON(jalanGeometri, {
                        style: function(feature) {
                            return {
                                color: 'blue',
                                weight: 4
                            }; // Warna biru untuk garis jalan
                        }
                    }).addTo(map);

                    map.fitBounds(geojsonLayer.getBounds()); // Sesuaikan tampilan peta agar garis terlihat
                } catch (e) {
                    console.error("Error saat menambahkan GeoJSON layer di Detail Laporan:", e);
                    // Jika ada error, set view default ke Jelobo
                    map.setView([-7.701469, 110.746014], 16);
                    console.warn("Karena error GeoJSON, peta diatur ke Desa Jelobo di Detail Laporan.");
                }
            } else {
                // Jika tidak ada geometri yang valid, set view ke Kantor Desa Jelobo
                map.setView([-7.701469, 110.746014], 16);
                console.log("Tidak ada geometri valid, peta diatur ke Desa Jelobo di Detail Laporan.");
            }
        });
    </script>
@endpush
