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
        Schema::create('temporary_work_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Crew member
            $table->foreignId('booked_by_user_id')->constrained('users')->cascadeOnDelete(); // Captain who booked
            
            // Booking status
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending');
            
            // Work details
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_hours');
            $table->text('work_description')->nullable();
            $table->json('requirements')->nullable(); // What crew needs to bring, etc.
            
            // Location
            $table->string('location')->nullable();
            $table->string('berth_details')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Compensation
            $table->decimal('day_rate', 10, 2);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('total_payment', 10, 2);
            $table->string('payment_currency', 3)->default('EUR');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'company_process'])->default('cash');
            $table->string('payment_timing')->nullable(); // "End of day", "Weekly", etc.
            $table->boolean('payment_received')->default(false);
            $table->timestamp('payment_received_at')->nullable();
            
            // Contact info for the booking
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('whatsapp_available')->default(false);
            
            // Timeline
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Cancellation
            $table->enum('cancelled_by', ['crew', 'vessel', 'system'])->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->integer('hours_before_start')->nullable(); // How many hours before start was it cancelled
            
            // Ratings (both parties rate each other after completion)
            $table->boolean('crew_rated_vessel')->default(false);
            $table->boolean('vessel_rated_crew')->default(false);
            
            // Notes
            $table->text('crew_notes')->nullable(); // Private notes from crew
            $table->text('vessel_notes')->nullable(); // Private notes from vessel
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['job_post_id', 'status']);
            $table->index(['work_date', 'status']);
            $table->index('booked_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_work_bookings');
    }
};
