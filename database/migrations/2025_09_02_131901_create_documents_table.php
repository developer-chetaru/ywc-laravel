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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
			$table->boolean('is_active')->default(1);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->enum('type', ['passport','idvisa','certificate','other']);
            $table->string('file_path')->nullable();
            $table->string('file_type', 20)->nullable(); // pdf, jpg, png
            $table->integer('file_size')->nullable(); // in KB

          	$table->date('dob')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            // Versioning
            $table->integer('version')->default(1);

            // Audit trail
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes(); // keep history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
