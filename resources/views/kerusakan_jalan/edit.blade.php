{{-- resources/views/kerusakan_jalan/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Laporan Kerusakan: ' . ($kerusakanJalan->jalan->nama_jalan ?? 'N/A'))

@push('styles')
    {{-- Jika ada CSS khusus untuk form ini --}}
    <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}">
@endpush

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Edit Laporan Kerusakan Jalan</h6>
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
            <li class="fw-medium">Edit</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            <form action="{{ route('kerusakan-jalan.update', $kerusakanJalan->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Penting untuk metode PUT/PATCH --}}

                <div class="mb-3">
                    <label for="jalan_id" class="form-label">Nama Jalan</label>
                    <select class="form-select @error('jalan_id') is-invalid @enderror" id="jalan_id" name="jalan_id"
                        required {{ Auth::check() && Auth::user()->isPejabatDesa() ? 'disabled' : '' }}>
                        {{-- Pejabat Desa tidak bisa ganti jalan --}}
                        <option value="">Pilih Jalan</option>
                        @foreach ($jalans as $jalan)
                            <option value="{{ $jalan->id }}"
                                {{ old('jalan_id', $kerusakanJalan->jalan_id) == $jalan->id ? 'selected' : '' }}>
                                {{ $jalan->nama_jalan }} (Regional: {{ $jalan->regional->nama_regional ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('jalan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal_lapor" class="form-label">Tanggal Lapor</label>
                    <input type="text" class="form-control flatpickr @error('tanggal_lapor') is-invalid @enderror"
                        id="tanggal_lapor" name="tanggal_lapor"
                        value="{{ old('tanggal_lapor', $kerusakanJalan->tanggal_lapor->format('Y-m-d')) }}" required
                        {{ Auth::check() && Auth::user()->isPejabatDesa() ? 'disabled' : '' }}> {{-- Pejabat Desa tidak bisa ganti tanggal --}}
                    @error('tanggal_lapor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tingkat_kerusakan" class="form-label">Tingkat Kerusakan</label>
                    <select class="form-select @error('tingkat_kerusakan') is-invalid @enderror" id="tingkat_kerusakan"
                        name="tingkat_kerusakan" required
                        {{ Auth::check() && Auth::user()->isPejabatDesa() ? 'disabled' : '' }}> {{-- Pejabat Desa tidak bisa ganti tingkat kerusakan --}}
                        <option value="">Pilih Tingkat Kerusakan</option>
                        <option value="ringan"
                            {{ old('tingkat_kerusakan', $kerusakanJalan->tingkat_kerusakan) == 'ringan' ? 'selected' : '' }}>
                            Ringan</option>
                        <option value="sedang"
                            {{ old('tingkat_kerusakan', $kerusakanJalan->tingkat_kerusakan) == 'sedang' ? 'selected' : '' }}>
                            Sedang</option>
                        <option value="berat"
                            {{ old('tingkat_kerusakan', $kerusakanJalan->tingkat_kerusakan) == 'berat' ? 'selected' : '' }}>
                            Berat</option>
                    </select>
                    @error('tingkat_kerusakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tingkat_lalu_lintas" class="form-label">Tingkat Lalu Lintas</label>
                    <select class="form-select @error('tingkat_lalu_lintas') is-invalid @enderror" id="tingkat_lalu_lintas"
                        name="tingkat_lalu_lintas" required
                        {{ Auth::check() && Auth::user()->isPejabatDesa() ? 'disabled' : '' }}> {{-- Pejabat Desa tidak bisa ganti lalu lintas --}}
                        <option value="">Pilih Tingkat Lalu Lintas</option>
                        <option value="rendah"
                            {{ old('tingkat_lalu_lintas', $kerusakanJalan->tingkat_lalu_lintas) == 'rendah' ? 'selected' : '' }}>
                            Rendah</option>
                        <option value="sedang"
                            {{ old('tingkat_lalu_lintas', $kerusakanJalan->tingkat_lalu_lintas) == 'sedang' ? 'selected' : '' }}>
                            Sedang</option>
                        <option value="tinggi"
                            {{ old('tingkat_lalu_lintas', $kerusakanJalan->tingkat_lalu_lintas) == 'tinggi' ? 'selected' : '' }}>
                            Tinggi</option>
                    </select>
                    @error('tingkat_lalu_lintas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="panjang_ruas_rusak" class="form-label">Panjang Ruas Rusak (meter)</label>
                    <input type="number" step="0.01"
                        class="form-control @error('panjang_ruas_rusak') is-invalid @enderror" id="panjang_ruas_rusak"
                        name="panjang_ruas_rusak"
                        value="{{ old('panjang_ruas_rusak', $kerusakanJalan->panjang_ruas_rusak) }}" required
                        {{ Auth::check() && Auth::user()->isPejabatDesa() ? 'disabled' : '' }}> {{-- Pejabat Desa tidak bisa ganti panjang rusak --}}
                    @error('panjang_ruas_rusak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi_kerusakan" class="form-label">Deskripsi Kerusakan (Opsional)</label>
                    <textarea class="form-control @error('deskripsi_kerusakan') is-invalid @enderror" id="deskripsi_kerusakan"
                        name="deskripsi_kerusakan" rows="3" {{ Auth::check() && Auth::user()->isPejabatDesa() ? 'disabled' : '' }}>{{ old('deskripsi_kerusakan', $kerusakanJalan->deskripsi_kerusakan) }}</textarea>
                    @error('deskripsi_kerusakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="foto_kerusakan" class="form-label">Foto Kerusakan (Opsional)</label>
                    @if ($kerusakanJalan->foto_kerusakan)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $kerusakanJalan->foto_kerusakan) }}" class="img-thumbnail"
                                alt="Foto Kerusakan Saat Ini" style="max-width: 200px;">
                            <small class="form-text text-muted d-block">Foto saat ini. Pilih file baru untuk
                                mengganti.</small>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('foto_kerusakan') is-invalid @enderror"
                        id="foto_kerusakan" name="foto_kerusakan" accept="image/*">
                    @error('foto_kerusakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Max 2MB, format: jpeg, png, jpg, gif.</small>
                </div>

                {{-- Bagian yang hanya bisa diedit oleh Admin --}}
                @if (Auth::check() && Auth::user()->isAdmin())
                    <hr class="my-4">
                    <h5 class="card-title mb-3">Update Status & Prioritas</h5>

                    <div class="mb-3">
                        <label for="status_perbaikan" class="form-label">Status Perbaikan</label>
                        <select class="form-select @error('status_perbaikan') is-invalid @enderror" id="status_perbaikan"
                            name="status_perbaikan" required>
                            <option value="belum diperbaiki"
                                {{ old('status_perbaikan', $kerusakanJalan->status_perbaikan) == 'belum diperbaiki' ? 'selected' : '' }}>
                                Belum Diperbaiki</option>
                            <option value="dalam perbaikan"
                                {{ old('status_perbaikan', $kerusakanJalan->status_perbaikan) == 'dalam perbaikan' ? 'selected' : '' }}>
                                Dalam Perbaikan</option>
                            <option value="sudah diperbaiki"
                                {{ old('status_perbaikan', $kerusakanJalan->status_perbaikan) == 'sudah diperbaiki' ? 'selected' : '' }}>
                                Sudah Diperbaiki</option>
                        </select>
                        @error('status_perbaikan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="klasifikasi_prioritas" class="form-label">Klasifikasi Prioritas</label>
                        <select class="form-select @error('klasifikasi_prioritas') is-invalid @enderror"
                            id="klasifikasi_prioritas" name="klasifikasi_prioritas">
                            <option value="">Belum Diklasifikasi</option>
                            <option value="tinggi"
                                {{ old('klasifikasi_prioritas', $kerusakanJalan->klasifikasi_prioritas) == 'tinggi' ? 'selected' : '' }}>
                                Tinggi</option>
                            <option value="sedang"
                                {{ old('klasifikasi_prioritas', $kerusakanJalan->klasifikasi_prioritas) == 'sedang' ? 'selected' : '' }}>
                                Sedang</option>
                            <option value="rendah"
                                {{ old('klasifikasi_prioritas', $kerusakanJalan->klasifikasi_prioritas) == 'rendah' ? 'selected' : '' }}>
                                Rendah</option>
                        </select>
                        @error('klasifikasi_prioritas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Akan diisi otomatis oleh sistem Naive Bayes.</small>
                    </div>
                @endif {{-- End if Admin --}}

                <button type="submit" class="btn btn-primary">Update Laporan</button>
                <a href="{{ route('kerusakan-jalan.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Flatpickr JS untuk date picker -->
    <script src="{{ asset('assets/js/lib/flatpickr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            flatpickr("#tanggal_lapor", {
                dateFormat: "Y-m-d", // Format tanggal sesuai kebutuhan
            });

            // Script auto-fill (hanya relevan di halaman create, tapi aman di sini)
            $('#jalan_id').change(function() {
                // ... (Logika auto-fill sama seperti di create.blade.php) ...
                var jalanId = $(this).val();
                if (jalanId) {
                    var apiUrl = '{{ route('api.jalan.get-data', ['jalan' => '__jalanId__']) }}'.replace(
                        '__jalanId__', jalanId);
                    $.ajax({
                        url: apiUrl,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            var suggestedTingkatKerusakan = data.suggested_tingkat_kerusakan;
                            $('#tingkat_kerusakan').val(suggestedTingkatKerusakan);
                            $('#panjang_ruas_rusak').val(data.suggested_panjang_ruas_rusak);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching jalan data:', error);
                            alert('Gagal mengambil data jalan. Silakan coba lagi.');
                        }
                    });
                }
            });
        });
    </script>
@endpush
