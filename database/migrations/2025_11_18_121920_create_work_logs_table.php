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
        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Date and Time
            $table->date('work_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('total_hours_worked', 5, 2)->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->integer('break_minutes')->default(0);
            
            // Rest Information
            $table->decimal('total_rest_hours', 5, 2)->default(0);
            $table->boolean('rest_uninterrupted')->default(true);
            $table->integer('sleep_hours')->nullable();
            
            // Location Status
            $table->enum('location_status', ['at_sea', 'in_port', 'in_yard', 'on_leave', 'shore_leave'])->default('at_sea');
            $table->string('location_name')->nullable();
            $table->string('port_name')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Yacht Information
            $table->string('yacht_name')->nullable();
            $table->string('yacht_type')->nullable();
            $table->string('yacht_length')->nullable();
            $table->string('yacht_flag')->nullable();
            $table->string('position_rank')->nullable();
            $table->string('department')->nullable();
            $table->string('captain_name')->nullable();
            $table->string('company_name')->nullable();
            
            // Conditions
            $table->string('weather_conditions')->nullable();
            $table->string('sea_state')->nullable();
            $table->string('visibility')->nullable();
            
            // Activities and Notes
            $table->json('activities')->nullable(); // Array of activities
            $table->text('notes')->nullable();
            $table->text('comments')->nullable();
            
            // Compliance
            $table->boolean('is_compliant')->default(true);
            $table->string('compliance_status')->default('compliant'); // compliant, warning, violation
            $table->text('compliance_notes')->nullable();
            
            // Sea Service
            $table->boolean('counts_towards_sea_service')->default(true);
            $table->boolean('is_at_sea')->default(true);
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->boolean('is_day_off')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->unique(['user_id', 'work_date']);
            $table->index(['user_id', 'work_date']);
            $table->index(['work_date', 'location_status']);
            $table->index('compliance_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_logs');
    }
};
