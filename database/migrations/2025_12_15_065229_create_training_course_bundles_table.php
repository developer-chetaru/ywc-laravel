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
        Schema::create('training_course_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('training_providers')->onDelete('cascade');
            $table->string('name'); // STCW Complete Package, Annual Renewal Bundle, etc.
            $table->text('description')->nullable();
            $table->json('course_ids'); // Array of provider_course_ids included
            $table->decimal('bundle_price', 10, 2);
            $table->decimal('bundle_discount_percentage', 5, 2)->nullable(); // Additional savings
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_course_bundles');
    }
};
