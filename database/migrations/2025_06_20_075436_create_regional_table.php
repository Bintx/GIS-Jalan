<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regional', function (Blueprint $table) {
            $table->id();
            $table->string('nama_regional');
            $table->enum('tipe_regional', ['RT', 'RW', 'Desa', 'Kecamatan'])->default('Desa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regional');
    }
};
