{{-- resources/views/jalan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Data Jalan') {{-- Judul halaman yang benar --}}

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Daftar Data Jalan</h6> {{-- Judul di dalam halaman --}}
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Data Jalan</li> {{-- Breadcrumb yang benar --}}
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            {{-- Notifikasi SweetAlert2 akan muncul di sini (dari layouts/app.blade.php) --}}

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Data Jalan</h5> {{-- Judul kartu --}}
                <a href="{{ route('jalan.create') }}" class="btn btn-primary btn-sm">Tambah Jalan</a>
            </div>

            {{-- Filter Form untuk Data Jalan --}}
            <form action="{{ route('jalan.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="filter_nama_jalan" class="form-label text-sm">Nama Jalan</label>
                        <input type="text" class="form-control form-control-sm" id="filter_nama_jalan" name="nama_jalan"
                            value="{{ $filterNamaJalan }}" placeholder="Cari nama jalan...">
                    </div>

                    <div class="col-md-3">
                        <label for="filter_kondisi_jalan" class="form-label text-sm">Kondisi Awal</label>
                        <select class="form-select form-select-sm" id="filter_kondisi_jalan" name="kondisi_jalan">
                            <option value="">Semua Kondisi</option>
                            <option value="baik" {{ $filterKondisiJalan === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ $filterKondisiJalan === 'rusak ringan' ? 'selected' : '' }}>
                                Rusak Ringan</option>
                            <option value="rusak sedang" {{ $filterKondisiJalan === 'rusak sedang' ? 'selected' : '' }}>
                                Rusak Sedang</option>
                            <option value="rusak berat" {{ $filterKondisiJalan === 'rusak berat' ? 'selected' : '' }}>Rusak
                                Berat</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter_regional_id" class="form-label text-sm">Regional</label>
                        <select class="form-select form-select-sm" id="filter_regional_id" name="regional_id">
                            <option value="">Semua Regional</option>
                            @foreach ($allRegionalsForFilter as $regional)
                                <option value="{{ $regional->id }}"
                                    {{ (string) $filterRegionalId === (string) $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nama_regional }} ({{ $regional->tipe_regional }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-secondary btn-sm me-2">Terapkan Filter</button>
                    <a href="{{ route('jalan.index') }}" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                </div>
            </form>
            {{-- End Filter Form --}}

            <div class="table-responsive">
                <table id="jalanTable" class="table table-bordered table-hover text-center"> {{-- Tambahkan text-center di sini --}}
                    <thead>
                        <tr>
                            <th class="text-center">#</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Nama Jalan</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Panjang (m)</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Kondisi Awal</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Regional RT</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Regional RW</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Regional Dusun</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Aksi</th> {{-- Tambahkan text-center --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jalans as $jalan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $jalan->nama_jalan }}</td>
                                <td>{{ $jalan->panjang_jalan }}</td>
                                <td>{{ $jalan->kondisi_jalan }}</td>
                                <td>{{ $jalan->regional->nama_regional ?? 'N/A' }}</td>
                                <td>{{ $jalan->rwRegional->nama_regional ?? 'N/A' }}</td>
                                <td>{{ $jalan->dusunRegional->nama_regional ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('jalan.show', $jalan->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                    <a href="{{ route('jalan.edit', $jalan->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('jalan.destroy', $jalan->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data jalan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $jalans->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Periksa apakah DataTables sudah diinisialisasi pada tabel ini
            if ($.fn.DataTable.isDataTable('#jalanTable')) {
                // Jika sudah, hancurkan instance lama sebelum membuat yang baru
                $('#jalanTable').DataTable().destroy();
            }

            // Inisialisasi DataTables pada tabel Anda
            $('#jalanTable').DataTable({
                "paging": true, // Aktifkan paginasi
                "ordering": true, // Aktifkan sorting kolom
                "info": true, // Tampilkan info paginasi
                "searching": false, // <-- NONAKTIFKAN KOTAK PENCARIAN GLOBAL
                "lengthChange": false // <-- NONAKTIFKAN "Show X entries" dropdown
            });
        });
    </script>
@endpush
