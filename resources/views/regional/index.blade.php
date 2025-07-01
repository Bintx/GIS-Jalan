{{-- resources/views/regional/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Master Regional')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Data Master Regional</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Data Regional</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">
            {{-- Notifikasi SweetAlert2 akan muncul di sini --}}

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Regional</h5>
                <a href="{{ route('regional.create') }}" class="btn btn-primary btn-sm">Tambah Regional</a>
            </div>

            {{-- Filter Form (Ini akan tetap berfungsi di sisi server) --}}
            <form action="{{ route('regional.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    {{-- <div class="col-md-4">
                        <label for="filter_nama_regional" class="form-label text-sm">Nama Regional</label>
                        <input type="text" class="form-control form-control-sm" id="filter_nama_regional"
                            name="nama_regional" value="{{ $filterNamaRegional }}" placeholder="Cari nama regional...">
                    </div> --}}

                    <div class="col-md-4">
                        <label for="filter_tipe_regional" class="form-label text-sm">Tipe Regional</label>
                        <select class="form-select form-select-sm" id="filter_tipe_regional" name="tipe_regional">
                            <option value="">Semua Tipe</option>
                            <option value="RT" {{ $filterTipeRegional === 'RT' ? 'selected' : '' }}>RT</option>
                            <option value="RW" {{ $filterTipeRegional === 'RW' ? 'selected' : '' }}>RW</option>
                            <option value="Dusun" {{ $filterTipeRegional === 'Dusun' ? 'selected' : '' }}>Dusun</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-secondary btn-sm me-2">Terapkan Filter</button>
                    <a href="{{ route('regional.index') }}" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                </div>
            </form>
            {{-- End Filter Form --}}

            <div class="table-responsive">
                <table id="regionalTable" class="table table-bordered table-hover text-center"> {{-- Tambahkan text-center di sini --}}
                    <thead>
                        <tr>
                            <th class="text-center">#</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Tipe Regional</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Nama Regional</th> {{-- Tambahkan text-center --}}
                            <th class="text-center">Aksi</th> {{-- Tambahkan text-center --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($regionals as $regional)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $regional->tipe_regional }}</td>
                                <td>{{ $regional->nama_regional }}</td>
                                <td>
                                    <a href="{{ route('regional.edit', $regional->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('regional.destroy', $regional->id) }}" method="POST"
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
                                <td colspan="4" class="text-center">Tidak ada data regional.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginasi Laravel bawaan DIHAPUS karena DataTables akan menangani paginasi di sisi klien --}}
            {{-- {{ $regionals->links() }} --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Periksa apakah DataTables sudah diinisialisasi pada tabel ini
            if ($.fn.DataTable.isDataTable('#regionalTable')) {
                // Jika sudah, hancurkan instance lama sebelum membuat yang baru
                $('#regionalTable').DataTable().destroy();
            }

            // Inisialisasi DataTables pada tabel Anda
            $('#regionalTable').DataTable({
                "paging": true, // Aktifkan paginasi
                "ordering": true, // Aktifkan sorting kolom
                "info": true, // Tampilkan info paginasi
                "searching": false, // <-- NONAKTIFKAN KOTAK PENCARIAN GLOBAL
                "lengthChange": false // <-- NONAKTIFKAN "Show X entries" dropdown
            });
        });
    </script>
@endpush
