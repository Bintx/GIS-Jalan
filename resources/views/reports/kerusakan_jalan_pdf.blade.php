<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kerusakan Jalan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-size: 10pt;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 0.3em 0.6em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        .text-small {
            font-size: 8pt;
        }

        /* img {
            max-width: 100px;
            height: auto;
            border-radius: 3px;
        } */
        /* Dikomentari karena gambar tidak akan ditampilkan */
    </style>
</head>

<body>
    <div class="container">
        <h1>Laporan Kerusakan Jalan Desa Jelobo</h1>
        <p class="text-small">Tanggal Cetak: {{ date('d M Y H:i:s') }}</p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jalan</th>
                    <th>Regional</th>
                    <th>Tgl Lapor</th>
                    <th>Pelapor</th>
                    <th>Kerusakan</th>
                    <th>Lalu Lintas</th>
                    <th>Panjang Rusak</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    {{-- <th>Foto</th> --}} {{-- Dihapus --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($kerusakanJalans as $laporan)
                    <tr>
                        <td>{{ $laporan->id }}</td>
                        <td>{{ $laporan->jalan->nama_jalan ?? 'N/A' }}</td>
                        <td>{{ $laporan->jalan->regional->nama_regional ?? 'N/A' }}</td>
                        <td>{{ $laporan->tanggal_lapor->format('d/m/Y') }}</td>
                        <td>{{ $laporan->user->name ?? 'N/A' }}</td>
                        <td>{{ $laporan->tingkat_kerusakan }}</td>
                        <td>{{ $laporan->tingkat_lalu_lintas }}</td>
                        <td>{{ $laporan->panjang_ruas_rusak }} m</td>
                        <td>
                            @php
                                $priorityClass = '';
                                if ($laporan->klasifikasi_prioritas == 'tinggi') {
                                    $priorityClass = 'badge-danger';
                                } elseif ($laporan->klasifikasi_prioritas == 'sedang') {
                                    $priorityClass = 'badge-warning';
                                } elseif ($laporan->klasifikasi_prioritas == 'rendah') {
                                    $priorityClass = 'badge-success';
                                } else {
                                    $priorityClass = 'badge-secondary';
                                }
                            @endphp
                            <span
                                class="badge {{ $priorityClass }}">{{ strtoupper($laporan->klasifikasi_prioritas ?? 'Belum') }}</span>
                        </td>
                        <td>
                            @php
                                $statusClass = '';
                                if ($laporan->status_perbaikan == 'belum diperbaiki') {
                                    $statusClass = 'badge-danger';
                                } elseif ($laporan->status_perbaikan == 'dalam perbaikan') {
                                    $statusClass = 'badge-warning';
                                } elseif ($laporan->status_perbaikan == 'sudah diperbaiki') {
                                    $statusClass = 'badge-success';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ strtoupper($laporan->status_perbaikan) }}</span>
                        </td>
                        {{-- <td class="text-center"> --}} {{-- Dihapus --}}
                        {{-- @if ($laporan->foto_kerusakan)
                                <img src="{{ public_path('storage/' . $laporan->foto_kerusakan) }}" alt="Foto">
                            @else
                                -
                            @endif
                        </td> --}} {{-- Dihapus --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
