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
        Schema::create('mental_health_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->string('mood_tag')->nullable();
            $table->json('tags')->nullable();
            $table->string('entry_type')->default('freeform'); // freeform, prompt_based, gratitude, thought_record
            $table->foreignId('prompt_id')->nullable(); // If using a prompt
            $table->boolean('is_shareable_with_therapist')->default(false);
            $table->foreignId('shared_with_therapist_id')->nullable()->constrained('mental_health_therapists')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_journals');
    }
};
