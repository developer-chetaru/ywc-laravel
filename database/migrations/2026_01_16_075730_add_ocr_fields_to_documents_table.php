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
            $table->enum('ocr_status', ['pending', 'processing', 'completed', 'failed'])
                  ->default('pending')
                  ->after('status');
            $table->decimal('ocr_confidence', 5, 2)
                  ->nullable()
                  ->after('ocr_status');
            $table->json('ocr_data')
                  ->nullable()
                  ->after('ocr_confidence');
            $table->text('ocr_error')
                  ->nullable()
                  ->after('ocr_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['ocr_status', 'ocr_confidence', 'ocr_data', 'ocr_error']);
        });
    }
};
