    {{-- resources/views/regional/create.blade.php --}}
    @extends('layouts.app')

    @section('title', 'Tambah Regional Baru')

    @section('content')
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Tambah Regional Baru</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">
                    <a href="{{ route('regional.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        Data Regional
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Tambah</li>
            </ul>
        </div>

        <div class="card h-100">
            <div class="card-body p-24">
                <form action="{{ route('regional.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_regional" class="form-label">Nama Regional</label>
                        <input type="text" class="form-control @error('nama_regional') is-invalid @enderror"
                            id="nama_regional" name="nama_regional" value="{{ old('nama_regional') }}" required>
                        @error('nama_regional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tipe_regional" class="form-label">Tipe Regional</label>
                        <select class="form-select @error('tipe_regional') is-invalid @enderror" id="tipe_regional"
                            name="tipe_regional" required>
                            <option value="">Pilih Tipe</option>
                            <option value="RT" {{ old('tipe_regional') == 'RT' ? 'selected' : '' }}>RT</option>
                            <option value="RW" {{ old('tipe_regional') == 'RW' ? 'selected' : '' }}>RW</option>
                            <option value="Dusun" {{ old('tipe_regional') == 'Dusun' ? 'selected' : '' }}>Dusun</option>
                            {{-- Perubahan di sini --}}
                        </select>
                        @error('tipe_regional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('regional.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    @endsection
