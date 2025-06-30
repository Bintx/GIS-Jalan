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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Laporan Kerusakan</h5>
                <div>
                    {{-- Tombol Tambah Laporan (Admin & Pejabat Desa bisa masuk route ini) --}}
                    {{-- Menggunakan @can('pejabat_desa') --}}
                    <a href="{{ route('kerusakan-jalan.create') }}" class="btn btn-primary btn-sm me-2">Tambah Laporan</a>

                    {{-- Tombol Export PDF (Hanya Admin) --}}
                    @can('admin')
                        {{-- Menggunakan @can('admin') --}}
                        <a href="{{ route('kerusakan-jalan.export-pdf') }}" class="btn btn-info btn-sm me-2">Export PDF</a>
                    @endcan

                    {{-- Tombol Export Excel (Hanya Admin) --}}
                    @can('admin')
                        {{-- Menggunakan @can('admin') --}}
                        <a href="{{ route('kerusakan-jalan.export-excel') }}" class="btn btn-success btn-sm">Export Excel</a>
                    @endcan
                </div>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('kerusakan-jalan.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="filter_nama_jalan" class="form-label text-sm">Nama Jalan</label>
                        <input type="text" class="form-control form-control-sm" id="filter_nama_jalan" name="nama_jalan"
                            value="{{ $filterNamaJalan }}" placeholder="Cari nama jalan...">
                    </div>

                    <div class="col-md-3">
                        <label for="filter_tingkat_kerusakan" class="form-label text-sm">Tingkat Kerusakan</label>
                        <select class="form-select form-select-sm" id="filter_tingkat_kerusakan" name="tingkat_kerusakan">
                            <option value="">Semua Tingkat</option>
                            <option value="ringan" {{ $filterTingkatKerusakan === 'ringan' ? 'selected' : '' }}>Ringan
                            </option>
                            <option value="sedang" {{ $filterTingkatKerusakan === 'sedang' ? 'selected' : '' }}>Sedang
                            </option>
                            <option value="berat" {{ $filterTingkatKerusakan === 'berat' ? 'selected' : '' }}>Berat
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter_prioritas" class="form-label text-sm">Prioritas</label>
                        <select class="form-select form-select-sm" id="filter_prioritas" name="prioritas">
                            <option value="">Semua Prioritas</option>
                            <option value="tinggi" {{ $filterPrioritas === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="sedang" {{ $filterPrioritas === 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="rendah" {{ $filterPrioritas === 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="belum_diklasifikasi"
                                {{ $filterPrioritas === 'belum_diklasifikasi' ? 'selected' : '' }}>Belum Diklasifikasi
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter_status" class="form-label text-sm">Status Perbaikan</label>
                        <select class="form-select form-select-sm" id="filter_status" name="status_perbaikan">
                            <option value="">Semua Status</option>
                            <option value="belum_diperbaiki"
                                {{ $filterStatusPerbaikan === 'belum_diperbaiki' ? 'selected' : '' }}>Belum Diperbaiki
                            </option>
                            <option value="dalam_perbaikan"
                                {{ $filterStatusPerbaikan === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan
                            </option>
                            <option value="sudah_diperbaiki"
                                {{ $filterStatusPerbaikan === 'sudah_diperbaiki' ? 'selected' : '' }}>Sudah Diperbaiki
                            </option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-secondary btn-sm me-2">Terapkan Filter</button>
                    <a href="{{ route('kerusakan-jalan.index') }}" class="btn btn-outline-secondary btn-sm">Reset
                        Filter</a>
                </div>
            </form>

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
                                    <a href="{{ route('kerusakan-jalan.edit', $laporan->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    {{-- Tombol Edit dan Hapus: Logika UI ini sesuai dengan logika di controller --}}
                                    @if (Auth::check() && Auth::user()->isAdmin())
                                        <form action="{{ route('kerusakan-jalan.destroy', $laporan->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">Hapus</button>
                                        </form>
                                    @endif
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
