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
        Schema::create('work_schedule_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yacht_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            // Template Information
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', [
                'charter_week',
                'passage',
                'shipyard',
                'port_rotation',
                'watch_schedule',
                'custom'
            ])->default('custom');
            
            // Schedule Pattern (can be single day or multi-day pattern)
            $table->json('schedule_pattern')->nullable(); // Array of day schedules
            
            // Default Settings
            $table->time('default_start_time')->nullable();
            $table->time('default_end_time')->nullable();
            $table->integer('default_break_minutes')->default(0);
            $table->enum('default_location_status', ['in_port', 'at_sea', 'in_shipyard', 'at_anchor'])->default('in_port');
            $table->enum('default_work_type', [
                'regular_duties',
                'maintenance',
                'guest_service',
                'emergency_standby',
                'shore_leave',
                'rest_period'
            ])->default('regular_duties');
            $table->string('default_department')->nullable();
            
            // Template Settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false); // Can be used by other yachts
            $table->integer('usage_count')->default(0);
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['yacht_id', 'is_active']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_templates');
    }
};

