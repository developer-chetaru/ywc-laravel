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
        Schema::create('rallies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['social', 'active', 'cultural', 'professional', 'learning', 'celebration'])->default('social');
            $table->enum('privacy', ['public', 'private', 'invite_only'])->default('public');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('location_name');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address')->nullable();
            $table->string('meeting_point')->nullable();
            $table->integer('max_participants')->nullable();
            $table->decimal('cost', 10, 2)->nullable()->default(0);
            $table->text('what_to_bring')->nullable();
            $table->text('requirements')->nullable();
            $table->text('contact_info')->nullable();
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->integer('views')->default(0);
            $table->decimal('rating', 3, 2)->nullable()->default(0);
            $table->integer('total_ratings')->default(0);
            $table->timestamps();

            $table->index(['organizer_id', 'status']);
            $table->index(['start_date', 'status']);
            $table->index('type');
            $table->index('privacy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rallies');
    }
};
