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
        Schema::create('training_provider_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('training_providers')->onDelete('cascade');
            $table->string('image_path');
            $table->string('category')->nullable(); // facilities, equipment, classroom, accommodation, location
            $table->text('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_provider_galleries');
    }
};
