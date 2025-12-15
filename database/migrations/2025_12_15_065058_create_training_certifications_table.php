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
        Schema::create('training_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('training_certification_categories')->onDelete('cascade');
            $table->string('name'); // STCW Basic Safety, ENG1, SSO, etc.
            $table->string('slug')->unique();
            $table->string('official_designation')->nullable(); // Official certification code
            $table->text('description');
            $table->text('prerequisites')->nullable();
            $table->integer('validity_period_months')->nullable(); // How long certification is valid
            $table->text('renewal_requirements')->nullable();
            $table->text('international_recognition')->nullable(); // Regulatory bodies
            $table->text('career_benefits')->nullable();
            $table->text('positions_requiring')->nullable(); // Which positions need this
            $table->json('related_certifications')->nullable(); // Related cert IDs
            $table->json('recommended_progression')->nullable(); // Next steps
            $table->string('cover_image')->nullable();
            $table->json('sample_certificates')->nullable(); // Array of certificate image paths
            $table->boolean('requires_admin_approval')->default(false); // For new certifications
            $table->boolean('is_active')->default(true);
            $table->integer('provider_count')->default(0); // Cache count of providers
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_certifications');
    }
};
