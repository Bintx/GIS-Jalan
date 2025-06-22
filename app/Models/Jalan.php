<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelPostgis\Eloquent\PostgisTrait; // Pastikan baris ini ada dan benar

class Jalan extends Model
{
    use HasFactory, PostgisTrait;

    protected $table = 'jalan';
    protected $fillable = [
        'nama_jalan',
        'panjang_jalan',
        'kondisi_jalan',
        'regional_id',
        'geometri', // Tambahkan ini
    ];

    // Definisikan tipe kolom PostGIS
    protected $postgisFields = [
        'geometri' => 'LineString', // Sesuai dengan tipe di migrasi Anda
        // Jika Anda punya kolom PostGIS lain, tambahkan di sini
        // 'location' => [
        //     'type' => 'Point',
        //     'geomtype' => 'Point',
        //     'srid' => 4326
        // ]
    ];

    // Relasi ke Regional
    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    // Relasi ke KerusakanJalan
    public function kerusakanJalans()
    {
        return $this->hasMany(KerusakanJalan::class);
    }
}
