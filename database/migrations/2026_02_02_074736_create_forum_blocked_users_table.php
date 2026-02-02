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
        Schema::create('forum_blocked_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // User who blocked
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete(); // User being blocked
            $table->timestamps();

            // Unique constraint: one block record per user pair
            $table->unique(['user_id', 'blocked_user_id']);
            
            // Indexes
            $table->index('user_id');
            $table->index('blocked_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_blocked_users');
    }
};
