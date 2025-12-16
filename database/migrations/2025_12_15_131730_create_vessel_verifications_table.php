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
        Schema::create('vessel_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Captain/User requesting verification
            $table->foreignId('yacht_id')->nullable()->constrained()->nullOnDelete(); // Optional yacht reference
            
            // Verification method
            $table->enum('verification_method', ['captain', 'management_company', 'hod_authorized'])->default('captain');
            
            // Vessel information
            $table->string('vessel_name');
            $table->string('imo_number')->nullable();
            $table->string('mmsi_number')->nullable();
            $table->string('flag_state')->nullable();
            $table->string('role_on_vessel'); // Captain, Owner, Management Rep, etc.
            $table->text('authority_description')->nullable(); // Proof of authority to hire
            
            // Documents uploaded
            $table->string('captain_license_path')->nullable();
            $table->string('vessel_registration_path')->nullable();
            $table->string('authorization_letter_path')->nullable();
            $table->string('management_company_docs_path')->nullable();
            
            // Verification status
            $table->enum('status', ['pending', 'under_review', 'verified', 'rejected', 'expired'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Verification can expire
            
            // Contact verification
            $table->string('verification_email')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_phone')->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('phone_verified_at')->nullable();
            
            // Reviewer info
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reviewer_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index('yacht_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessel_verifications');
    }
};
