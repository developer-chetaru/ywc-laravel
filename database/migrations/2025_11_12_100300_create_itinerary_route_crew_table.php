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
        Schema::create('itinerary_route_crew', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('itinerary_routes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->enum('role', ['owner', 'editor', 'viewer'])->default('viewer');
            $table->enum('status', ['pending', 'accepted', 'declined', 'revoked'])->default('pending');
            $table->boolean('notify_on_updates')->default(true);
            $table->json('permissions')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['route_id', 'user_id']);
            $table->unique(['route_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_route_crew');
    }
};

