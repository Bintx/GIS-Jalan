{{-- resources/views/kerusakan_jalan/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Lapor Kerusakan Jalan')

@push('styles')
    {{-- Jika ada CSS khusus untuk form ini --}}
    <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}">
@endpush

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Lapor Kerusakan Jalan</h6>
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
            <li class="fw-medium">Tambah</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            <form action="{{ route('kerusakan-jalan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="jalan_id" class="form-label">Nama Jalan</label>
                    <select class="form-select @error('jalan_id') is-invalid @enderror" id="jalan_id" name="jalan_id"
                        required>
                        <option value="">Pilih Jalan</option>
                        @foreach ($jalans as $jalan)
                            <option value="{{ $jalan->id }}" {{ old('jalan_id') == $jalan->id ? 'selected' : '' }}>
                                {{ $jalan->nama_jalan }} - (Kondisi Awal: {{ $jalan->kondisi_jalan }})
                                (RT: {{ $jalan->regional->nama_regional ?? 'N/A' }}
                                RW: {{ $jalan->rwRegional->nama_regional ?? 'N/A' }}
                                Dusun: {{ $jalan->dusunRegional->nama_regional ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('jalan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Area untuk menampilkan informasi Regional lengkap --}}
                <div id="regionalInfoDisplay" class="mb-3" style="display: none;">
                    <p class="form-text text-muted mb-1"><strong>Detail Regional Jalan:</strong></p>
                    <p class="mb-0 text-sm">RT: <span id="displayRtRegional"></span></p>
                    <p class="mb-0 text-sm">RW: <span id="displayRwRegional"></span></p>
                    <p class="mb-0 text-sm">Dusun: <span id="displayDusunRegional"></span></p>
                </div>

                <div class="mb-3">
                    <label for="tanggal_lapor" class="form-label">Tanggal Lapor</label>
                    <input type="text" class="form-control flatpickr @error('tanggal_lapor') is-invalid @enderror"
                        id="tanggal_lapor" name="tanggal_lapor" value="{{ old('tanggal_lapor', date('Y-m-d')) }}" required>
                    @error('tanggal_lapor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tingkat_kerusakan" class="form-label">Tingkat Kerusakan</label>
                    <select class="form-select @error('tingkat_kerusakan') is-invalid @enderror" id="tingkat_kerusakan"
                        name="tingkat_kerusakan" required>
                        <option value="">Pilih Tingkat Kerusakan</option>
                        <option value="ringan" {{ old('tingkat_kerusakan') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="sedang" {{ old('tingkat_kerusakan') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="berat" {{ old('tingkat_kerusakan') == 'berat' ? 'selected' : '' }}>Berat</option>
                    </select>
                    @error('tingkat_kerusakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tingkat_lalu_lintas" class="form-label">Tingkat Lalu Lintas</label>
                    <select class="form-select @error('tingkat_lalu_lintas') is-invalid @enderror" id="tingkat_lalu_lintas"
                        name="tingkat_lalu_lintas" required>
                        <option value="">Pilih Tingkat Lalu Lintas</option>
                        <option value="rendah" {{ old('tingkat_lalu_lintas') == 'rendah' ? 'selected' : '' }}>Rendah
                        </option>
                        <option value="sedang" {{ old('tingkat_lalu_lintas') == 'sedang' ? 'selected' : '' }}>Sedang
                        </option>
                        <option value="tinggi" {{ old('tingkat_lalu_lintas') == 'tinggi' ? 'selected' : '' }}>Tinggi
                        </option>
                    </select>
                    @error('tingkat_lalu_lintas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="panjang_ruas_rusak" class="form-label">Panjang Ruas Rusak (meter)</label>
                    <input type="number" step="0.01"
                        class="form-control @error('panjang_ruas_rusak') is-invalid @enderror" id="panjang_ruas_rusak"
                        name="panjang_ruas_rusak" value="{{ old('panjang_ruas_rusak') }}" required>
                    @error('panjang_ruas_rusak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi_kerusakan" class="form-label">Deskripsi Kerusakan (Opsional)</label>
                    <textarea class="form-control @error('deskripsi_kerusakan') is-invalid @enderror" id="deskripsi_kerusakan"
                        name="deskripsi_kerusakan" rows="3">{{ old('deskripsi_kerusakan') }}</textarea>
                    @error('deskripsi_kerusakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="foto_kerusakan" class="form-label">Foto Kerusakan (Opsional)</label>
                    <input type="file" class="form-control @error('foto_kerusakan') is-invalid @enderror"
                        id="foto_kerusakan" name="foto_kerusakan" accept="image/*">
                    @error('foto_kerusakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Max 2MB, format: jpeg, png, jpg, gif.</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
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

            $('#jalan_id').change(function() {
                var jalanId = $(this).val();
                var regionalInfoDisplay = $('#regionalInfoDisplay');
                var displayRtRegional = $('#displayRtRegional');
                var displayRwRegional = $('#displayRwRegional');
                var displayDusunRegional = $('#displayDusunRegional');

                if (jalanId) {
                    var apiUrl = '{{ route('api.jalan.get-data', ['jalan' => '__jalanId__']) }}'.replace(
                        '__jalanId__', jalanId);

                    $.ajax({
                        url: apiUrl,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            // Auto-fill Tingkat Kerusakan dan Panjang Ruas Rusak
                            var suggestedTingkatKerusakan = data.suggested_tingkat_kerusakan;
                            $('#tingkat_kerusakan').val(suggestedTingkatKerusakan);
                            $('#panjang_ruas_rusak').val(data.suggested_panjang_ruas_rusak);

                            // Tampilkan informasi Regional lengkap
                            displayRtRegional.text(data.regional_rt_nama);
                            displayRwRegional.text(data.regional_rw_nama);
                            displayDusunRegional.text(data.regional_dusun_nama);
                            regionalInfoDisplay.show(); // Tampilkan div info regional

                            console.log('Data jalan:', data);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching jalan data:', error);
                            alert('Gagal mengambil data jalan. Silakan coba lagi.');
                            regionalInfoDisplay.hide(); // Sembunyikan jika error
                        }
                    });
                } else {
                    // Kosongkan field dan sembunyikan info regional jika tidak ada jalan yang dipilih
                    $('#tingkat_kerusakan').val('');
                    $('#panjang_ruas_rusak').val('');
                    regionalInfoDisplay.hide();
                }
            });

            // Jalankan event change saat halaman dimuat jika ada nilai lama (misal setelah validasi gagal)
            if ($('#jalan_id').val()) {
                $('#jalan_id').trigger('change');
            }
        });
    </script>
@endpush
