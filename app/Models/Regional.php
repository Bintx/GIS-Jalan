<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasFactory;

    protected $table = 'regional'; // Pastikan nama tabel benar jika tidak plural
    protected $fillable = ['nama_regional', 'tipe_regional'];

    // Relasi ke Jalan
    public function jalans()
    {
        return $this->hasMany(Jalan::class);
    }
}
