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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('verification_level_id')->nullable()->after('status')
                  ->constrained('verification_levels')->onDelete('set null');
            $table->integer('highest_verification_level')->default(0)->after('verification_level_id'); // Track highest level achieved
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['verification_level_id']);
            $table->dropColumn(['verification_level_id', 'highest_verification_level']);
        });
    }
};
