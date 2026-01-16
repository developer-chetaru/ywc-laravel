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
        Schema::create('document_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->enum('old_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->enum('new_status', ['pending', 'approved', 'rejected']);
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('document_id');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_status_changes');
    }
};
