<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KerusakanJalan extends Model
{
    use HasFactory;

    protected $table = 'kerusakan_jalan';
    protected $fillable = [
        'jalan_id',
        'user_id',
        'tanggal_lapor',
        'tingkat_kerusakan',
        'tingkat_lalu_lintas',
        'panjang_ruas_rusak',
        'deskripsi_kerusakan',
        'foto_kerusakan',
        'status_perbaikan',
        'klasifikasi_prioritas',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
    ];

    // Relasi ke Jalan
    public function jalan()
    {
        return $this->belongsTo(Jalan::class);
    }

    // Relasi ke User (pelapor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
