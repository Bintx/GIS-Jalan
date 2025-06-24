{{-- resources/views/kerusakan_jalan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Laporan Kerusakan: ' . ($kerusakanJalan->jalan->nama_jalan ?? 'N/A'))

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

                <dt class="col-sm-4">Regional Jalan:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->jalan->regional->nama_regional ?? 'N/A' }}
                    ({{ $kerusakanJalan->jalan->regional->tipe_regional ?? 'N/A' }})</dd>

                <dt class="col-sm-4">Tanggal Lapor:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->tanggal_lapor->format('d M Y') }}</dd>

                <dt class="col-sm-4">Pelapor:</dt>
                <dd class="col-sm-8">{{ $kerusakanJalan->user->name ?? 'User Tidak Ditemukan' }}</dd>

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
                    @elseif ($kerusakanJalan->klasifikasi_prioritas == 'rendah')
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

            <div class="d-flex justify-content-end">
                @if (Auth::check() && Auth::user()->isAdmin())
                    <a href="{{ route('kerusakan-jalan.edit', $kerusakanJalan->id) }}" class="btn btn-warning me-2">Edit
                        Laporan</a>
                @endif
                <a href="{{ route('kerusakan-jalan.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
@endsection
