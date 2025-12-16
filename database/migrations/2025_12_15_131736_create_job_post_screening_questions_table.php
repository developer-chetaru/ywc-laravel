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
        Schema::create('job_post_screening_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            
            // Question details
            $table->integer('order')->default(0); // Order of questions (1, 2, 3, etc.)
            $table->text('question_text');
            $table->enum('question_type', ['text', 'textarea', 'number', 'select', 'multiselect'])->default('text');
            $table->json('options')->nullable(); // For select/multiselect questions
            $table->boolean('is_required')->default(true);
            $table->integer('max_length')->nullable(); // For text fields
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['job_post_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_post_screening_questions');
    }
};
