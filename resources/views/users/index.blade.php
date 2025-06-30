    {{-- resources/views/users/index.blade.php --}}
    @extends('layouts.app')

    @section('title', 'Manajemen Pengguna')

    @section('content')
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Manajemen Pengguna</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Pengguna</li>
            </ul>
        </div>

        <div class="card h-100">
            <div class="card-body p-24">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">Daftar Pengguna</h5>
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Tambah Pengguna Baru</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role == 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @elseif ($user->role == 'pejabat_desa')
                                            <span class="badge bg-info">Pejabat Desa</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        @if (Auth::id() !== $user->id)
                                            {{-- Tidak bisa menghapus diri sendiri --}}
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    @endsection
