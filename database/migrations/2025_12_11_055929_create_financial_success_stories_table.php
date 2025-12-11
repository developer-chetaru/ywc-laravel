<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_success_stories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Can be pseudonym
            $table->string('position')->nullable();
            $table->integer('age')->nullable();
            $table->enum('strategy_type', ['early_starter', 'late_starter', 'property_investor', 'aggressive_saver', 'other']);
            $table->text('story');
            $table->decimal('starting_point', 15, 2)->nullable();
            $table->decimal('current_status', 15, 2)->nullable();
            $table->text('advice')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_success_stories');
    }
};
