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
        Schema::create('agency_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'placed', 'unavailable'])->default('active');
            $table->string('desired_position')->nullable();
            $table->string('desired_vessel_type')->nullable();
            $table->decimal('desired_salary_min', 10, 2)->nullable();
            $table->decimal('desired_salary_max', 10, 2)->nullable();
            $table->date('available_from')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable()->comment('Skills, certifications, special qualifications');
            $table->integer('priority')->default(0)->comment('For agency ranking/sorting');
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['agency_id', 'status']);
            $table->index(['candidate_id', 'status']);
            $table->unique(['agency_id', 'candidate_id'], 'unique_agency_candidate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_candidates');
    }
};
