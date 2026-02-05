<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if category column already exists
        if (!Schema::hasColumn('document_types', 'category')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->string('category')->nullable()->after('name');
            });
        }

        // Clear existing document types and insert comprehensive list
        // First, set foreign key references to NULL to avoid constraint violations
        DB::table('documents')->whereNotNull('document_type_id')->update(['document_type_id' => null]);
        
        // Temporarily disable foreign key checks to allow deletion
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('document_types')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $documentTypes = [
            // STCW Certificates
            ['name' => 'STCW Basic Safety Training (BST)', 'category' => 'STCW Certificates', 'slug' => 'stcw-basic-safety-training', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Advanced Firefighting', 'category' => 'STCW Certificates', 'slug' => 'advanced-firefighting', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical First Aid / Medical Care', 'category' => 'STCW Certificates', 'slug' => 'medical-first-aid-care', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Proficiency in Survival Craft and Rescue Boats', 'category' => 'STCW Certificates', 'slug' => 'proficiency-survival-craft', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GMDSS (for radio operators)', 'category' => 'STCW Certificates', 'slug' => 'gmdss', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'STCW endorsements specific to flag state', 'category' => 'STCW Certificates', 'slug' => 'stcw-endorsements-flag-state', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Medical Certificates
            ['name' => 'ENG1 (UK standard - valid 2 years, 1 year over age 40)', 'category' => 'Medical Certificates', 'slug' => 'eng1', 'icon' => 'medical', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PEME (Pre-Employment Medical Examination)', 'category' => 'Medical Certificates', 'slug' => 'peme', 'icon' => 'medical', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 11, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yellow Fever vaccination records', 'category' => 'Medical Certificates', 'slug' => 'yellow-fever-vaccination', 'icon' => 'medical', 'requires_expiry_date' => true, 'requires_document_number' => false, 'requires_issuing_authority' => true, 'sort_order' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other region-specific medical certifications', 'category' => 'Medical Certificates', 'slug' => 'other-medical-certifications', 'icon' => 'medical', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 13, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Identity & Travel Documents
            ['name' => 'Passport(s) - with expiry tracking', 'category' => 'Identity & Travel Documents', 'slug' => 'passport', 'icon' => 'passport', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Seaman\'s Book / Discharge Book', 'category' => 'Identity & Travel Documents', 'slug' => 'seamans-book', 'icon' => 'id-card', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 21, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Visas (Schengen, US B1/B2, etc.)', 'category' => 'Identity & Travel Documents', 'slug' => 'visas', 'icon' => 'id-card', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 22, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Seafarer\'s Identity Document (SID)', 'category' => 'Identity & Travel Documents', 'slug' => 'seafarers-identity-document', 'icon' => 'id-card', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 23, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Professional Qualifications
            ['name' => 'USCG licenses (for US waters)', 'category' => 'Professional Qualifications', 'slug' => 'uscg-licenses', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MCA licenses (UK)', 'category' => 'Professional Qualifications', 'slug' => 'mca-licenses', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 31, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Officer of the Watch (OOW)', 'category' => 'Professional Qualifications', 'slug' => 'officer-of-watch', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 32, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chief Mate, Master licenses', 'category' => 'Professional Qualifications', 'slug' => 'chief-mate-master-licenses', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 33, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Engineering certificates', 'category' => 'Professional Qualifications', 'slug' => 'engineering-certificates', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 34, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MEOL (Marine Engine Operator License)', 'category' => 'Professional Qualifications', 'slug' => 'meol', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 35, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PSA (Port State Awareness)', 'category' => 'Professional Qualifications', 'slug' => 'psa', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 36, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ISM/ISPS Security training', 'category' => 'Professional Qualifications', 'slug' => 'ism-isps-security', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 37, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yacht Rating certificates', 'category' => 'Professional Qualifications', 'slug' => 'yacht-rating-certificates', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 38, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tender Driving licenses', 'category' => 'Professional Qualifications', 'slug' => 'tender-driving-licenses', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 39, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Water sports instructor qualifications', 'category' => 'Professional Qualifications', 'slug' => 'water-sports-instructor', 'icon' => 'certificate', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wine certifications (for interior crew)', 'category' => 'Professional Qualifications', 'slug' => 'wine-certifications', 'icon' => 'certificate', 'requires_expiry_date' => false, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 41, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Culinary qualifications', 'category' => 'Professional Qualifications', 'slug' => 'culinary-qualifications', 'icon' => 'certificate', 'requires_expiry_date' => false, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 42, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Employment Records
            ['name' => 'Contracts and addendums', 'category' => 'Employment Records', 'slug' => 'contracts-addendums', 'icon' => 'contract', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'References from previous yachts', 'category' => 'Employment Records', 'slug' => 'references', 'icon' => 'reference', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 51, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Discharge letters', 'category' => 'Employment Records', 'slug' => 'discharge-letters', 'icon' => 'document', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 52, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Non-disclosure agreements', 'category' => 'Employment Records', 'slug' => 'non-disclosure-agreements', 'icon' => 'document', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 53, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Work permits', 'category' => 'Employment Records', 'slug' => 'work-permits', 'icon' => 'id-card', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 54, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tax documentation', 'category' => 'Employment Records', 'slug' => 'tax-documentation', 'icon' => 'document', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 55, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Insurance & Financial
            ['name' => 'P&I (Protection & Indemnity) coverage documents', 'category' => 'Insurance & Financial', 'slug' => 'pi-coverage', 'icon' => 'insurance', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 60, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Private health insurance', 'category' => 'Insurance & Financial', 'slug' => 'private-health-insurance', 'icon' => 'insurance', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 61, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Life insurance', 'category' => 'Insurance & Financial', 'slug' => 'life-insurance', 'icon' => 'insurance', 'requires_expiry_date' => false, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 62, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pension documentation', 'category' => 'Insurance & Financial', 'slug' => 'pension-documentation', 'icon' => 'document', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => false, 'sort_order' => 63, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Additional Documents
            ['name' => 'Driving licenses (various countries)', 'category' => 'Additional Documents', 'slug' => 'driving-licenses', 'icon' => 'id-card', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => true, 'sort_order' => 70, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Criminal background checks / DBS checks', 'category' => 'Additional Documents', 'slug' => 'criminal-background-checks', 'icon' => 'document', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => true, 'sort_order' => 71, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'COVID vaccination records', 'category' => 'Additional Documents', 'slug' => 'covid-vaccination-records', 'icon' => 'medical', 'requires_expiry_date' => false, 'requires_document_number' => false, 'requires_issuing_authority' => true, 'sort_order' => 72, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Crew medical insurance cards', 'category' => 'Additional Documents', 'slug' => 'crew-medical-insurance-cards', 'icon' => 'insurance', 'requires_expiry_date' => true, 'requires_document_number' => true, 'requires_issuing_authority' => false, 'sort_order' => 73, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('document_types')->insert($documentTypes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
