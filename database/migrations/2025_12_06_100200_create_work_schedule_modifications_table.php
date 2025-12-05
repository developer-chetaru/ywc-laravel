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
        Schema::create('work_schedule_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('work_schedules')->cascadeOnDelete();
            $table->foreignId('modified_by')->constrained('users')->cascadeOnDelete();
            
            // Modification Details
            $table->enum('modification_type', [
                'time_adjustment',
                'location_change',
                'work_type_change',
                'cancellation',
                'extension',
                'shortening',
                'other'
            ]);
            
            // Change Tracking
            $table->json('changes_before')->nullable(); // Original values
            $table->json('changes_after')->nullable(); // New values
            
            // Reason
            $table->enum('reason_code', [
                'weather_delay',
                'guest_request',
                'maintenance_priority',
                'emergency',
                'crew_request',
                'itinerary_change',
                'other'
            ])->nullable();
            $table->text('reason_description')->nullable();
            
            // Variance Tracking
            $table->decimal('hours_variance', 5, 2)->default(0); // Positive = overtime, Negative = under-work
            $table->enum('variance_type', ['overtime', 'under_work', 'none'])->default('none');
            
            // Approval (if required)
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_approved')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['schedule_id', 'created_at']);
            $table->index('modification_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_modifications');
    }
};

