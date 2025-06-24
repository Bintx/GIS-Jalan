<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jalan extends Model
{
    use HasFactory;

    // Menentukan nama tabel jika tidak mengikuti konvensi plural Laravel
    protected $table = 'jalan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_jalan',
        'panjang_jalan',
        'kondisi_jalan',
        'regional_id',
        'geometri_json', // Kolom untuk menyimpan data geometri dalam format JSON
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'geometri_json' => 'array', // Mengkonversi kolom 'geometri_json' menjadi array PHP secara otomatis
    ];

    /**
     * Get the regional that owns the Jalan.
     */
    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    /**
     * Get the road damages (KerusakanJalan) for the Jalan.
     */
    public function kerusakanJalans()
    {
        return $this->hasMany(KerusakanJalan::class);
    }
}
