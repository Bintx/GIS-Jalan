<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jalan extends Model
{
    use HasFactory;

    protected $table = 'jalan';

    protected $fillable = [
        'nama_jalan',
        'panjang_jalan',
        'kondisi_jalan',
        'regional_id',      // Ini untuk ID RT
        'rw_regional_id',   // Kolom baru untuk ID RW
        'dusun_regional_id', // Kolom baru untuk ID Dusun
        'geometri_json',
    ];

    protected $casts = [
        'geometri_json' => 'array',
    ];

    /**
     * Get the RT regional that owns the Jalan. (Using existing regional_id)
     */
    public function regional()
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    /**
     * Get the RW regional that owns the Jalan. (New relationship)
     */
    public function rwRegional()
    {
        return $this->belongsTo(Regional::class, 'rw_regional_id');
    }

    /**
     * Get the Dusun regional that owns the Jalan. (New relationship)
     */
    public function dusunRegional()
    {
        return $this->belongsTo(Regional::class, 'dusun_regional_id');
    }

    /**
     * Get the road damages (KerusakanJalan) for the Jalan.
     */
    public function kerusakanJalans()
    {
        return $this->hasMany(KerusakanJalan::class);
    }
}
