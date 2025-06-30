<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jalan', function (Blueprint $table) {
            // Kita sudah punya regional_id (untuk RT), tambahkan untuk RW dan Dusun
            // Set as nullable jika ada jalan yang mungkin tidak punya data ini
            $table->foreignId('rw_regional_id')->nullable()->constrained('regional')->onDelete('set null');
            $table->foreignId('dusun_regional_id')->nullable()->constrained('regional')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jalan', function (Blueprint $table) {
            $table->dropForeign(['rw_regional_id']);
            $table->dropColumn('rw_regional_id');
            $table->dropForeign(['dusun_regional_id']);
            $table->dropColumn('dusun_regional_id');
        });
    }
};
