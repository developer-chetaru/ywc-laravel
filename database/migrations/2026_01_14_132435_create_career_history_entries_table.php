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
        Schema::create('career_history_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Vessel Information
            $table->string('vessel_name');
            $table->string('position_title');
            $table->enum('vessel_type', [
                'motor_yacht',
                'sailing_yacht',
                'explorer_yacht',
                'catamaran',
                'commercial_vessel',
                'other'
            ])->nullable();
            $table->string('vessel_flag', 3)->nullable(); // ISO country code
            $table->decimal('vessel_length_meters', 8, 2)->nullable();
            $table->integer('gross_tonnage')->nullable();
            
            // Position Details
            $table->date('start_date');
            $table->date('end_date')->nullable(); // NULL = current position
            $table->enum('employment_type', [
                'permanent',
                'seasonal',
                'temporary',
                'rotational_contract'
            ])->nullable();
            $table->enum('position_rank', [
                'captain',
                'officer',
                'junior_crew',
                'support_staff'
            ])->nullable();
            $table->enum('department', [
                'deck',
                'engineering',
                'interior',
                'galley',
                'other'
            ])->nullable();
            
            // Employment Information
            $table->string('employer_company')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_contact')->nullable();
            $table->text('key_duties')->nullable(); // Max 500 chars
            $table->text('notable_achievements')->nullable(); // Max 500 chars
            $table->enum('departure_reason', [
                'contract_end',
                'new_opportunity',
                'personal_reasons',
                'vessel_sold',
                'other'
            ])->nullable();
            
            // Documentation Links
            $table->foreignId('reference_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('contract_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('signoff_document_id')->nullable()->constrained('documents')->nullOnDelete();
            
            // Visibility & Display
            $table->boolean('visible_on_profile')->default(true);
            $table->integer('display_order')->default(0); // For manual sorting
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'start_date']);
            $table->index(['user_id', 'visible_on_profile']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_history_entries');
    }
};
