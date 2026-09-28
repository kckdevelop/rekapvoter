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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email'); // 'admin' | 'saksi'
            $table->foreignId('tps_id')->nullable()->after('role')->constrained('tps')->nullOnDelete();
            $table->string('phone')->nullable()->after('tps_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tps_id']);
            $table->dropColumn(['role', 'tps_id', 'phone']);
        });
    }
};
