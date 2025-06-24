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
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Daftar Regional</h5>
                <a href="{{ route('regional.create') }}" class="btn btn-primary btn-sm">Tambah Regional</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Regional</th>
                            <th>Tipe Regional</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($regionals as $regional)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $regional->nama_regional }}</td>
                                <td>{{ $regional->tipe_regional }}</td>
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

            <div class="mt-4">
                {{ $regionals->links() }}
            </div>
        </div>
    </div>
@endsection
