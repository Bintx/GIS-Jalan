{{-- resources/views/kerusakan_jalan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Laporan Kerusakan Jalan')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Laporan Kerusakan Jalan</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Laporan Kerusakan</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Laporan Kerusakan</h5>
                <div>
                    <a href="{{ route('kerusakan-jalan.create') }}" class="btn btn-primary btn-sm me-2">Tambah Laporan</a>
                    <a href="{{ route('kerusakan-jalan.export-pdf') }}" class="btn btn-info btn-sm me-2">Export PDF</a>
                    <a href="{{ route('kerusakan-jalan.export-excel') }}" class="btn btn-success btn-sm">Export Excel</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Jalan</th>
                            <th>Tanggal Lapor</th>
                            <th>Pelapor</th>
                            <th>Tingkat Kerusakan</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kerusakanJalans as $laporan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $laporan->jalan->nama_jalan ?? 'Jalan Tidak Ditemukan' }}</td>
                                <td>{{ $laporan->tanggal_lapor->format('d M Y') }}</td>
                                <td>{{ $laporan->user->name ?? 'User Tidak Ditemukan' }}</td>
                                <td>{{ $laporan->tingkat_kerusakan }}</td>
                                <td>
                                    @if ($laporan->klasifikasi_prioritas == 'tinggi')
                                        <span class="badge bg-danger">Tinggi</span>
                                    @elseif ($laporan->klasifikasi_prioritas == 'sedang')
                                        <span class="badge bg-warning">Sedang</span>
                                    @elseif ($laporan->klasifikasi_prioritas == 'rendah')
                                        <span class="badge bg-success">Rendah</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Diklasifikasi</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($laporan->status_perbaikan == 'belum diperbaiki')
                                        <span class="badge bg-danger">Belum Diperbaiki</span>
                                    @elseif ($laporan->status_perbaikan == 'dalam perbaikan')
                                        <span class="badge bg-warning">Dalam Perbaikan</span>
                                    @elseif ($laporan->status_perbaikan == 'sudah diperbaiki')
                                        <span class="badge bg-success">Sudah Diperbaiki</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kerusakan-jalan.show', $laporan->id) }}"
                                        class="btn btn-info btn-sm">Lihat</a>
                                    {{-- Hanya admin yang bisa edit status perbaikan/prioritas --}}
                                    @if (Auth::check() && Auth::user()->isAdmin())
                                        <a href="{{ route('kerusakan-jalan.edit', $laporan->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                    @endif
                                    <form action="{{ route('kerusakan-jalan.destroy', $laporan->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada laporan kerusakan jalan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $kerusakanJalans->links() }}
            </div>
        </div>
    </div>
@endsection
