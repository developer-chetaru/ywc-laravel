<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_educational_content', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['course', 'guide', 'strategy_template']);
            $table->text('description')->nullable();
            $table->text('content')->nullable(); // Full content or course structure
            $table->string('difficulty')->nullable(); // beginner, intermediate, advanced
            $table->integer('duration_minutes')->nullable();
            $table->json('modules')->nullable(); // For courses
            $table->string('file_path')->nullable(); // PDF, video, etc.
            $table->boolean('is_published')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_educational_content');
    }
};
