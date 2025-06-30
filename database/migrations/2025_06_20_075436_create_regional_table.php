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
            Schema::create('regional', function (Blueprint $table) {
                $table->id();
                $table->string('nama_regional');
                // Ubah enum dari ['RT', 'RW', 'Desa', 'Kecamatan'] menjadi ['RT', 'RW', 'Dusun']
                $table->enum('tipe_regional', ['RT', 'RW', 'Dusun'])->default('Dusun');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('regional');
        }
    };
