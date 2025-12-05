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
        Schema::table('work_logs', function (Blueprint $table) {
            // Link to schedule
            $table->foreignId('schedule_id')->nullable()->after('user_id')->constrained('work_schedules')->nullOnDelete();
            
            // Confirmation tracking
            $table->boolean('is_schedule_confirmed')->default(false)->after('is_verified');
            $table->timestamp('schedule_confirmed_at')->nullable()->after('is_schedule_confirmed');
            
            // Variance tracking
            $table->decimal('scheduled_hours', 5, 2)->nullable()->after('total_hours_worked');
            $table->decimal('hours_variance', 5, 2)->default(0)->after('scheduled_hours');
            $table->enum('variance_type', ['overtime', 'under_work', 'none'])->default('none')->after('hours_variance');
            
            // Modification tracking
            $table->boolean('was_modified')->default(false)->after('variance_type');
            $table->text('modification_reason')->nullable()->after('was_modified');
            
            // Indexes
            $table->index('schedule_id');
            $table->index('is_schedule_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_logs', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn([
                'schedule_id',
                'is_schedule_confirmed',
                'schedule_confirmed_at',
                'scheduled_hours',
                'hours_variance',
                'variance_type',
                'was_modified',
                'modification_reason',
            ]);
        });
    }
};

