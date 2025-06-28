@extends('layouts.app')

@section('title', 'Dashboard - Sistem Informasi Perbaikan Jalan')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Dashboard Overview</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Sistem Informasi Perbaikan Jalan</li>
        </ul>
    </div>

    {{-- Bagian Statistik Umum --}}
    <div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-md-2 row-cols-1 gy-4 mb-4">
        {{-- Total Jalan Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-1 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Jalan</p>
                            <h6 class="mb-0">{{ $totalJalan }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:road-map-point-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Laporan Kerusakan Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Laporan Kerusakan</p>
                            <h6 class="mb-0">{{ $totalLaporanKerusakan }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:danger-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Pengguna Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-3 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Pengguna</p>
                            <h6 class="mb-0">{{ $totalUsers }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Admin Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-5 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Admin</p>
                            <h6 class="mb-0">{{ $adminUsers }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:user-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Pejabat Desa Card --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Pejabat Desa</p>
                            <h6 class="mb-0">{{ $pejabatDesaUsers }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="fluent:people-20-filled" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Prioritas Laporan --}}
    <div class="row row-cols-xxl-3 row-cols-lg-2 row-cols-1 gy-4 mt-4">
        <div class="col">
            <div class="card h-100 border">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-3">Laporan Berdasarkan Prioritas</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Prioritas Tinggi
                            <span class="badge bg-danger rounded-pill">{{ $prioritasTinggi }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Prioritas Sedang
                            <span class="badge bg-warning rounded-pill">{{ $prioritasSedang }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Prioritas Rendah
                            <span class="badge bg-success rounded-pill">{{ $prioritasRendah }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Belum Diklasifikasi
                            <span class="badge bg-secondary rounded-pill">{{ $prioritasBelumDiklasifikasi }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Statistik Status Perbaikan --}}
        <div class="col">
            <div class="card h-100 border">
                <div class="card-body p-24">
                    <h6 class="fw-semibold mb-3">Laporan Berdasarkan Status Perbaikan</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Belum Diperbaiki
                            <span class="badge bg-danger rounded-pill">{{ $statusBelumDiperbaiki }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dalam Perbaikan
                            <span class="badge bg-warning rounded-pill">{{ $statusDalamPerbaikan }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sudah Diperbaiki
                            <span class="badge bg-success rounded-pill">{{ $statusSudahDiperbaiki }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
