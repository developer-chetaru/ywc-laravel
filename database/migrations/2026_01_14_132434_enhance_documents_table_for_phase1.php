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
        Schema::table('documents', function (Blueprint $table) {
            // Add document_type_id foreign key (nullable for backward compatibility)
            $table->foreignId('document_type_id')->nullable()->after('type')->constrained('document_types')->nullOnDelete();
            
            // Add new Phase 1 fields
            $table->string('document_name')->nullable()->after('document_type_id'); // User-friendly name
            $table->string('document_number')->nullable()->after('document_name');
            $table->string('issuing_authority')->nullable()->after('document_number');
            $table->string('issuing_country', 3)->nullable()->after('issuing_authority'); // ISO country code
            $table->text('notes')->nullable()->after('issuing_country');
            $table->json('tags')->nullable()->after('notes'); // For additional organization
            $table->boolean('featured_on_profile')->default(false)->after('tags');
            $table->string('thumbnail_path')->nullable()->after('featured_on_profile');
            
            // Keep legacy 'type' enum for backward compatibility but make it nullable
            // We'll migrate data from enum to document_type_id
        });

        // Migrate existing data: Map legacy enum types to new document_types
        \DB::statement("
            UPDATE documents d
            INNER JOIN document_types dt ON 
                CASE d.type
                    WHEN 'passport' THEN dt.slug = 'passport'
                    WHEN 'idvisa' THEN dt.slug = 'ids-visas'
                    WHEN 'certificate' THEN dt.slug = 'certificates'
                    WHEN 'other' THEN dt.slug = 'other'
                END
            SET d.document_type_id = dt.id
            WHERE d.document_type_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['document_type_id']);
            $table->dropColumn([
                'document_type_id',
                'document_name',
                'document_number',
                'issuing_authority',
                'issuing_country',
                'notes',
                'tags',
                'featured_on_profile',
                'thumbnail_path',
            ]);
        });
    }
};
