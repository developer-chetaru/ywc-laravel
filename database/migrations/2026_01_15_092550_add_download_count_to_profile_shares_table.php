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
        Schema::table('profile_shares', function (Blueprint $table) {
            // Rename access_count to view_count if it exists
            if (Schema::hasColumn('profile_shares', 'access_count')) {
                $table->renameColumn('access_count', 'view_count');
            } else {
                $table->integer('view_count')->default(0)->after('is_active');
            }
            
            // Add download_count if it doesn't exist
            if (!Schema::hasColumn('profile_shares', 'download_count')) {
                $table->integer('download_count')->default(0)->after('view_count');
            }
            
            // Add ip_address if it doesn't exist
            if (!Schema::hasColumn('profile_shares', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('last_accessed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_shares', function (Blueprint $table) {
            if (Schema::hasColumn('profile_shares', 'download_count')) {
                $table->dropColumn('download_count');
            }
            if (Schema::hasColumn('profile_shares', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            // Note: We don't rename back to avoid issues
        });
    }
};
