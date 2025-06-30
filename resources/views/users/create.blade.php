    {{-- resources/views/users/create.blade.php --}}
    @extends('layouts.app')

    @section('title', 'Tambah Pengguna Baru')

    @section('content')
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Tambah Pengguna Baru</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">
                    <a href="{{ route('users.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        Manajemen Pengguna
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Tambah</li>
            </ul>
        </div>

        <div class="card h-100">
            <div class="card-body p-24">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Peran (Role)</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                            required>
                            <option value="">Pilih Peran</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="pejabat_desa" {{ old('role') == 'pejabat_desa' ? 'selected' : '' }}>Pejabat Desa
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    @endsection
