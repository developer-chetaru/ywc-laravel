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
        Schema::table('document_shares', function (Blueprint $table) {
            // Add IP address tracking
            if (!Schema::hasColumn('document_shares', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('last_accessed_at');
            }
            
            // Add download count tracking
            if (!Schema::hasColumn('document_shares', 'download_count')) {
                $table->integer('download_count')->default(0)->after('access_count');
            }
            
            // Add hashed token for security (store both for backward compatibility)
            if (!Schema::hasColumn('document_shares', 'token_hash')) {
                $table->string('token_hash', 255)->nullable()->after('share_token');
                $table->index('token_hash');
            }
            
            // Add password protection (optional)
            if (!Schema::hasColumn('document_shares', 'password_hash')) {
                $table->string('password_hash')->nullable()->after('token_hash');
            }
            
            // Add report abuse tracking
            if (!Schema::hasColumn('document_shares', 'abuse_reports')) {
                $table->integer('abuse_reports')->default(0)->after('download_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_shares', function (Blueprint $table) {
            if (Schema::hasColumn('document_shares', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            if (Schema::hasColumn('document_shares', 'download_count')) {
                $table->dropColumn('download_count');
            }
            if (Schema::hasColumn('document_shares', 'token_hash')) {
                $table->dropIndex(['token_hash']);
                $table->dropColumn('token_hash');
            }
            if (Schema::hasColumn('document_shares', 'password_hash')) {
                $table->dropColumn('password_hash');
            }
            if (Schema::hasColumn('document_shares', 'abuse_reports')) {
                $table->dropColumn('abuse_reports');
            }
        });
    }
};
