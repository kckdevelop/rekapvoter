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
        Schema::table('tps', function (Blueprint $table) {
            $table->unsignedInteger('suara_kandidat')->nullable()->after('nama_tps');
            $table->unsignedInteger('suara_lawan')->nullable()->after('suara_kandidat');
            $table->unsignedInteger('suara_tidak_sah')->nullable()->after('suara_lawan');
            $table->boolean('is_submitted')->default(false)->after('suara_tidak_sah');
            $table->string('catatan_saksi')->nullable()->after('is_submitted');
            $table->timestamp('waktu_input_real')->nullable()->after('catatan_saksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tps', function (Blueprint $table) {
            $table->dropColumn([
                'suara_kandidat',
                'suara_lawan',
                'suara_tidak_sah',
                'is_submitted',
                'catatan_saksi',
                'waktu_input_real',
            ]);
        });
    }
};
