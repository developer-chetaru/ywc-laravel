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
        Schema::create('share_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name'); // Template name (e.g., "Job Application", "Compliance Check")
            $table->text('description')->nullable();
            $table->json('document_criteria')->nullable(); // Filter criteria (type, status, etc.)
            $table->json('permissions')->nullable(); // can_view, can_download, can_print, etc.
            $table->integer('expiry_duration_days')->default(30); // Default expiry in days
            $table->text('default_message')->nullable(); // Default message for sharing
            $table->boolean('is_default')->default(false); // Mark as default template
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_templates');
    }
};
