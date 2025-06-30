<?php

namespace App\Exports;

use App\Models\KerusakanJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
// use Maatwebsite\Excel\Concerns\WithDrawings; // Dihapus
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing; // Dihapus
use Illuminate\Support\Facades\Storage; // Tetap diperlukan untuk methods lain jika ada

class KerusakanJalanExport implements FromCollection, WithHeadings, WithMapping // WithDrawings Dihapus
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return KerusakanJalan::with(['jalan.regional', 'user'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Jalan',
            'Regional',
            'Tipe Regional',
            'Tanggal Lapor',
            'Pelapor',
            'Tingkat Kerusakan',
            'Tingkat Lalu Lintas',
            'Panjang Ruas Rusak (m)',
            'Deskripsi',
            'Prioritas Klasifikasi',
            'Status Perbaikan',
            // 'Foto URL', // Dihapus
        ];
    }

    /**
     * @param mixed $laporan
     * @return array
     */
    public function map($laporan): array
    {
        return [
            $laporan->id,
            $laporan->jalan->nama_jalan ?? 'N/A',
            $laporan->jalan->regional->nama_regional ?? 'N/A',
            $laporan->jalan->regional->tipe_regional ?? 'N/A',
            $laporan->tanggal_lapor->format('Y-m-d'),
            $laporan->user->name ?? 'N/A',
            $laporan->tingkat_kerusakan,
            $laporan->tingkat_lalu_lintas,
            $laporan->panjang_ruas_rusak,
            $laporan->deskripsi_kerusakan,
            $laporan->klasifikasi_prioritas ?? 'Belum Diklasifikasi',
            $laporan->status_perbaikan,
            // $laporan->foto_kerusakan ? asset('storage/' . $laporan->foto_kerusakan) : '', // Dihapus
        ];
    }

    // Metode drawings() Dihapus sepenuhnya
    /*
    public function drawings(): array
    {
        $drawings = [];
        $rowNumber = 2;

        foreach ($this->collection() as $laporan) {
            if ($laporan->foto_kerusakan && Storage::disk('public')->exists($laporan->foto_kerusakan)) {
                $drawing = new Drawing();
                $drawing->setName('Foto Kerusakan ID ' . $laporan->id);
                $drawing->setDescription('Foto Kerusakan Jalan');
                $drawing->setPath(Storage::disk('public')->path($laporan->foto_kerusakan));
                $drawing->setHeight(50);
                $drawing->setCoordinates('M' . $rowNumber);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawings[] = $drawing;
            }
            $rowNumber++;
        }

        return $drawings;
    }
    */
}
