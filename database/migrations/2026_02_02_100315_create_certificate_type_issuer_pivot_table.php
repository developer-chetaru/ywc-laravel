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
        // Check if table already exists (might have been created manually or in previous migration)
        if (Schema::hasTable('certificate_type_issuer')) {
            // Table already exists, skip creation
            return;
        }
        
        Schema::create('certificate_type_issuer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_type_id')->constrained('certificate_types')->onDelete('cascade');
            $table->foreignId('certificate_issuer_id')->constrained('certificate_issuers')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate relationships
            $table->unique(['certificate_type_id', 'certificate_issuer_id'], 'cert_type_issuer_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_type_issuer');
    }
};
