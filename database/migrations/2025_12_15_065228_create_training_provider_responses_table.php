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
        Schema::create('training_provider_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('training_course_reviews')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('training_providers')->onDelete('cascade');
            $table->text('response_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_provider_responses');
    }
};
