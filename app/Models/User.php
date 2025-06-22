<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Tambahkan ini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi ke KerusakanJalan (jika seorang user bisa melaporkan banyak kerusakan)
    public function kerusakanJalan()
    {
        return $this->hasMany(KerusakanJalan::class);
    }

    // Helper untuk mengecek role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPejabatDesa()
    {
        return $this->role === 'pejabat_desa';
    }
}
