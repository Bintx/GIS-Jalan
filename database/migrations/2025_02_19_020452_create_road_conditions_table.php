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
        Schema::create('road_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('road_id')->constrained()->onDelete('cascade');
            $table->string('condition'); // Misal: Baik, Sedang, Rusak
            $table->integer('priority_level'); // Prioritas perbaikan (1-5)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('road_conditions');
    }
};
