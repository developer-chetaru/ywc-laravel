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
        Schema::create('crewdentials_syncs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sync_type')->comment('import, export, verification_request, verification_result');
            $table->string('direction')->comment('to_crewdentials, from_crewdentials');
            $table->string('status')->default('pending')->comment('pending, processing, completed, failed');
            $table->string('crewdentials_document_id', 500)->nullable()->comment('Document ID in Crewdentials system');
            $table->text('crewdentials_response')->nullable()->comment('Full JSON response from Crewdentials');
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['document_id', 'status']);
            $table->index('sync_type');
            $table->index('crewdentials_document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crewdentials_syncs');
    }
};
