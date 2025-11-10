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
        Schema::create('idvisa_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();

            $table->string('document_name', 100)->nullable(); // Schengen visa, Driving license
            $table->string('document_number', 50)->nullable();
            $table->string('issue_country', 100);             // Full country name
            $table->string('country_code', 3);                // ISO Alpha-3 (e.g., USA, IND, GBR)
            $table->string('visa_type', 50)->nullable();      // e.g., Tourist, Work, Student
            $table->string('place_of_issue', 100)->nullable();
          	$table->date('dob')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idvisa_details');
    }
};
