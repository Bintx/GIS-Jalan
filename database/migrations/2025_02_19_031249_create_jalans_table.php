<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jalan');
            $table->string('lokasi');
            $table->float('panjang');
            $table->float('lebar');
            $table->enum('tipe_perkerasan', ['aspal', 'beton', 'tanah']);
            $table->enum('status', ['nasional', 'provinsi', 'kabupaten', 'desa']);
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jalans');
    }
};
