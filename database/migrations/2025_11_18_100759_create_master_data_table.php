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
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // route_visibility, route_status, marina_type, yacht_type, country
            $table->string('code')->nullable(); // For countries: country code (US, GB, etc.)
            $table->string('name'); // Display name
            $table->text('description')->nullable(); // Optional description
            $table->integer('sort_order')->default(0); // For ordering
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // For additional data (country details, etc.)
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['type', 'is_active']);
            $table->index(['type', 'sort_order']);
            $table->unique(['type', 'code']); // Ensure unique code per type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
