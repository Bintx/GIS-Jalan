<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Road extends Model
{
    use HasFactory; // Pastikan ini ada

    // Tambahkan fillable jika diperlukan
    protected $fillable = ['name', 'description', 'latitude', 'longitude'];
}