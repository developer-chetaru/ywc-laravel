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
        Schema::table('document_verifications', function (Blueprint $table) {
            $table->string('certificate_number', 32)->nullable()->unique()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_verifications', function (Blueprint $table) {
            $table->dropColumn('certificate_number');
        });
    }
};
