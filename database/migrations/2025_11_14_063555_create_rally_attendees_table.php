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
        Schema::create('rally_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rally_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('rsvp_status', ['going', 'maybe', 'cant_go', 'interested'])->default('interested');
            $table->integer('guests_count')->default(0);
            $table->text('comment')->nullable();
            $table->boolean('checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            $table->unique(['rally_id', 'user_id']);
            $table->index(['rally_id', 'rsvp_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rally_attendees');
    }
};
