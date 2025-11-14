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
        Schema::create('user_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('User being recommended');
            $table->foreignId('recommender_id')->constrained('users')->cascadeOnDelete()->comment('User giving recommendation');
            $table->text('recommendation')->comment('Full recommendation text');
            $table->string('position')->nullable()->comment('Position held when working together');
            $table->string('yacht_name')->nullable();
            $table->integer('duration_months')->nullable();
            $table->date('work_start_date')->nullable();
            $table->date('work_end_date')->nullable();
            $table->boolean('would_work_again')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index('recommender_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_recommendations');
    }
};
