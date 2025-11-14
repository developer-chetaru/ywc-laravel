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
        Schema::create('user_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('connected_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined', 'blocked'])->default('pending');
            $table->text('request_message')->nullable();
            $table->string('category')->nullable()->comment('Professional, Social, etc.');
            $table->json('tags')->nullable()->comment('Custom tags for organization');
            $table->integer('connection_strength')->default(1)->comment('1-4: New, Regular, Strong, Close');
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'connected_user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['connected_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_connections');
    }
};
