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
        Schema::create('employer_crew', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('crew_id')->constrained('users')->onDelete('cascade');
            $table->string('position')->nullable()->comment('Crew position/role');
            $table->string('vessel_name')->nullable()->comment('Name of vessel');
            $table->string('vessel_imo')->nullable()->comment('IMO number of vessel');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'terminated'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['employer_id', 'status']);
            $table->index(['crew_id', 'status']);
            $table->unique(['employer_id', 'crew_id'], 'unique_employer_crew');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_crew');
    }
};
