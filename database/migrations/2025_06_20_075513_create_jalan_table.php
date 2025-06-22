<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Grimzy\LaravelPostgis\Support\Facades\Postgis;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jalan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jalan');
            $table->float('panjang_jalan');
            $table->enum('kondisi_jalan', ['baik', 'rusak ringan', 'rusak sedang', 'rusak berat']);
            $table->foreignId('regional_id')->constrained('regional')->onDelete('cascade');
            $table->timestamps();
            $table->geometry('geometri', 'LineString', 4326)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jalan');
    }
};
