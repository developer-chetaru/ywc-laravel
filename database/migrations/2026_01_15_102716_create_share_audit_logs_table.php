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
        // Check if table exists
        if (Schema::hasTable('share_audit_logs')) {
            // If table exists, check if duplicate index exists and drop it
            $connection = Schema::getConnection();
            $databaseName = $connection->getDatabaseName();
            
            $indexExists = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = 'share_audit_logs' 
                 AND index_name = 'share_audit_logs_shareable_type_shareable_id_index'",
                [$databaseName]
            );
            
            if ($indexExists[0]->count > 0) {
                // Drop the duplicate index if it exists
                $connection->statement("ALTER TABLE `share_audit_logs` DROP INDEX `share_audit_logs_shareable_type_shareable_id_index`");
            }
            return;
        }
        
        Schema::create('share_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('shareable'); // Polymorphic: document_share_id or profile_share_id (morphs() already creates index on shareable_type and shareable_id)
            $table->string('share_type'); // 'document' or 'profile'
            $table->string('action'); // 'created', 'accessed', 'downloaded', 'revoked', 'expired', 'abuse_reported'
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->text('details')->nullable(); // JSON or text for additional info
            $table->timestamps();

            // Indexes (morphs() already creates index on shareable_type and shareable_id, so don't duplicate it)
            $table->index('share_type');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_audit_logs');
    }
};
