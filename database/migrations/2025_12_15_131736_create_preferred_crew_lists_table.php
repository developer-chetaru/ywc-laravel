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
        Schema::create('preferred_crew_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Captain/Vessel owner
            $table->foreignId('crew_user_id')->constrained('users')->cascadeOnDelete(); // Crew member
            $table->foreignId('yacht_id')->nullable()->constrained()->nullOnDelete();
            
            // Relationship info
            $table->integer('times_worked_together')->default(0);
            $table->integer('captain_rating')->nullable(); // Captain's private rating 1-5
            $table->text('notes')->nullable(); // Captain's private notes
            $table->boolean('is_favorite')->default(false); // Mark as favorite
            
            // Notification preferences
            $table->boolean('notify_when_available')->default(true);
            $table->boolean('priority_access')->default(false); // Give priority access to new opportunities
            
            // Statistics (auto-calculated)
            $table->timestamp('first_hired_at')->nullable();
            $table->timestamp('last_hired_at')->nullable();
            $table->timestamp('last_worked_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['user_id', 'crew_user_id']); // One entry per captain-crew pair
            $table->index(['user_id', 'is_favorite']);
            $table->index(['crew_user_id', 'notify_when_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferred_crew_lists');
    }
};
