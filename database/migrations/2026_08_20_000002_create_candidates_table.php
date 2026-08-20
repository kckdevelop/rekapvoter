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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_urut')->unique();
            $table->string('nama');
            $table->boolean('is_main_candidate')->default(false);
            $table->string('warna_badge')->default('#059669');
            $table->timestamps();
        });

        Schema::create('tps_candidate_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tps_id')->constrained('tps')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->unsignedInteger('jumlah_suara')->default(0);
            $table->timestamps();

            $table->unique(['tps_id', 'candidate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tps_candidate_results');
        Schema::dropIfExists('candidates');
    }
};
