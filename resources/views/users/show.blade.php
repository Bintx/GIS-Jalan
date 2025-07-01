    {{-- resources/views/users/show.blade.php --}}
    @extends('layouts.app')

    @section('title', 'Detail Pengguna: ' . $user->name)

    @section('content')
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Detail Pengguna</h6>
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
                <li class="fw-medium">Detail</li>
            </ul>
        </div>

        <div class="card h-100">
            <div class="card-body p-24">
                <h5 class="card-title mb-4">Informasi Pengguna</h5>
                <dl class="row mb-4">
                    <dt class="col-sm-3">Nama:</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd>

                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>

                    <dt class="col-sm-3">Role:</dt>
                    <dd class="col-sm-9">
                        @if ($user->role == 'admin')
                            <span class="badge bg-primary">Admin</span>
                        @elseif ($user->role == 'pejabat_desa')
                            <span class="badge bg-info">Pejabat Desa</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Terdaftar Sejak:</dt>
                    <dd class="col-sm-9">{{ $user->created_at->format('d M Y H:i') }}</dd>

                    <dt class="col-sm-3">Email Terverifikasi:</dt>
                    <dd class="col-sm-9">
                        {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : 'Belum' }}</dd>
                </dl>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning me-2">Edit</a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    @endsection
