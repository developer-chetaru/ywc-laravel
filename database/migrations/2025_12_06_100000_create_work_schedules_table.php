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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('yacht_id')->nullable()->constrained()->nullOnDelete();
            
            // Schedule Date
            $table->date('schedule_date');
            
            // Time Planning
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('break_minutes')->default(0);
            $table->decimal('planned_hours', 5, 2)->default(0);
            
            // Location Context
            $table->enum('location_status', ['in_port', 'at_sea', 'in_shipyard', 'at_anchor'])->default('in_port');
            $table->string('location_name')->nullable();
            
            // Work Type Classification
            $table->enum('work_type', [
                'regular_duties',
                'maintenance',
                'guest_service',
                'emergency_standby',
                'shore_leave',
                'rest_period'
            ])->default('regular_duties');
            
            // Department
            $table->string('department')->nullable();
            
            // Schedule Status
            $table->enum('status', ['pending', 'confirmed', 'modified', 'cancelled'])->default('pending');
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            
            // Template Reference (if created from template) - Foreign key added in separate migration
            $table->unsignedBigInteger('template_id')->nullable();
            
            // Created by (captain or crew)
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('created_by_role', ['captain', 'crew', 'hod'])->default('crew');
            
            // Notes
            $table->text('notes')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'schedule_date']);
            $table->index(['schedule_date', 'status']);
            $table->index(['yacht_id', 'schedule_date']);
            $table->unique(['user_id', 'schedule_date']); // One schedule per user per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};

