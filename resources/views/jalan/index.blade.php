{{-- resources/views/jalan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Master Jalan')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Data Master Jalan</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Data Jalan</li>
        </ul>
    </div>

    <div class="card h-100">
        <div class="card-body p-24">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Jalan</h5>
                <a href="{{ route('jalan.create') }}" class="btn btn-primary btn-sm">Tambah Jalan</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Jalan</th>
                            <th>Panjang (m)</th>
                            <th>Kondisi Awal</th>
                            <th>Regional</th>
                            <th>Aksi</th>
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
                                <td>
                                    <a href="{{ route('jalan.show', $jalan->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                    <a href="{{ route('jalan.edit', $jalan->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('jalan.destroy', $jalan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data jalan.</td>
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
