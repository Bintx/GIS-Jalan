@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Data Jalan</h2>
    <a href="{{ route('jalan.create') }}" class="btn btn-primary">Tambah Jalan</a>
    <table class="table mt-3">
        <tr>
            <th>Nama Jalan</th>
            <th>Lokasi</th>
            <th>Panjang</th>
            <th>Lebar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach($jalans as $jalan)
        <tr>
            <td>{{ $jalan->nama_jalan }}</td>
            <td>{{ $jalan->lokasi }}</td>
            <td>{{ $jalan->panjang }} km</td>
            <td>{{ $jalan->lebar }} m</td>
            <td>{{ $jalan->status }}</td>
            <td>
                <a href="{{ route('jalan.edit', $jalan->id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('jalan.destroy', $jalan->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
