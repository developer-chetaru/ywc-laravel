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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('position');
            $table->string('vessel_name')->nullable();
            $table->string('vessel_type')->nullable();
            $table->string('vessel_flag')->nullable();
            $table->string('location')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('contract_duration')->nullable();
            $table->json('required_certificates')->nullable();
            $table->json('required_skills')->nullable();
            $table->text('additional_requirements')->nullable();
            $table->date('start_date')->nullable();
            $table->date('application_deadline')->nullable();
            $table->enum('status', ['open', 'closed', 'filled', 'draft'])->default('open');
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['posted_by', 'status']);
            $table->index('status');
            $table->index('application_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
