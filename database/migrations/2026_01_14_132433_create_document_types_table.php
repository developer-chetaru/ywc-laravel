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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name (e.g., "Passport", "Certificates")
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->string('icon')->nullable(); // Icon class or path
            $table->boolean('requires_expiry_date')->default(false);
            $table->boolean('requires_document_number')->default(false);
            $table->boolean('requires_issuing_authority')->default(false);
            $table->integer('sort_order')->default(0); // For UI display order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default document types
        \DB::table('document_types')->insert([
            ['name' => 'Passport', 'slug' => 'passport', 'icon' => 'passport', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Certificates', 'slug' => 'certificates', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IDs & Visas', 'slug' => 'ids-visas', 'icon' => 'id-card', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'References', 'slug' => 'references', 'icon' => 'reference', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contracts', 'slug' => 'contracts', 'icon' => 'contract', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Payslips', 'slug' => 'payslips', 'icon' => 'payslip', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Insurance', 'slug' => 'insurance', 'icon' => 'insurance', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Travel Documents', 'slug' => 'travel-documents', 'icon' => 'travel', 'requires_expiry_date' => true, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 8, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'slug' => 'other', 'icon' => 'document', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 9, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
