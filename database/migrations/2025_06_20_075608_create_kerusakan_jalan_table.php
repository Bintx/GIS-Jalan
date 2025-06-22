<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kerusakan_jalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jalan_id')->constrained('jalan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siapa yang melaporkan
            $table->date('tanggal_lapor');
            $table->enum('tingkat_kerusakan', ['ringan', 'sedang', 'berat']); // Input Naive Bayes
            $table->enum('tingkat_lalu_lintas', ['rendah', 'sedang', 'tinggi']); // Input Naive Bayes
            $table->float('panjang_ruas_rusak');
            $table->text('deskripsi_kerusakan')->nullable();
            $table->string('foto_kerusakan')->nullable(); // Path ke foto
            $table->enum('status_perbaikan', ['belum diperbaiki', 'dalam perbaikan', 'sudah diperbaiki'])->default('belum diperbaiki');
            $table->enum('klasifikasi_prioritas', ['tinggi', 'sedang', 'rendah'])->nullable(); // Hasil Naive Bayes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kerusakan_jalan');
    }
};
