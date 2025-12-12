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
        Schema::create('mental_health_therapist_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('mental_health_therapists')->onDelete('cascade');
            $table->string('reference_name');
            $table->string('reference_title')->nullable();
            $table->string('reference_organization')->nullable();
            $table->string('reference_email')->nullable();
            $table->string('reference_phone')->nullable();
            $table->text('relationship')->nullable();
            $table->integer('years_known')->nullable();
            $table->text('reference_notes')->nullable();
            $table->boolean('contacted')->default(false);
            $table->dateTime('contacted_at')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_therapist_references');
    }
};
