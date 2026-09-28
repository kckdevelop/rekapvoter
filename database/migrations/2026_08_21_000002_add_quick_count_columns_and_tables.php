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
            $table->unsignedInteger('quick_suara_kandidat')->nullable()->after('waktu_input_real');
            $table->unsignedInteger('quick_suara_lawan')->nullable()->after('quick_suara_kandidat');
            $table->unsignedInteger('quick_suara_tidak_sah')->nullable()->after('quick_suara_lawan');
            $table->boolean('quick_is_submitted')->default(false)->after('quick_suara_tidak_sah');
            $table->string('quick_catatan_saksi')->nullable()->after('quick_is_submitted');
            $table->timestamp('quick_waktu_input')->nullable()->after('quick_catatan_saksi');
        });

        Schema::create('tps_quick_candidate_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tps_id')->constrained('tps')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->unsignedInteger('jumlah_suara')->default(0);
            $table->timestamps();

            $table->unique(['tps_id', 'candidate_id'], 'tps_quick_cand_uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tps_quick_candidate_results');

        Schema::table('tps', function (Blueprint $table) {
            $table->dropColumn([
                'quick_suara_kandidat',
                'quick_suara_lawan',
                'quick_suara_tidak_sah',
                'quick_is_submitted',
                'quick_catatan_saksi',
                'quick_waktu_input',
            ]);
        });
    }
};
