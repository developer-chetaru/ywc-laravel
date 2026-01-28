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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'qrcode')) {
                $table->string('qrcode', 2048)->nullable()->after('profile_photo_path');
            }
        });
        
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_url')) {
                // Add profile_url after qrcode if qrcode exists, otherwise after profile_photo_path
                if (Schema::hasColumn('users', 'qrcode')) {
                    $table->string('profile_url', 2048)->nullable()->after('qrcode');
                } else {
                    $table->string('profile_url', 2048)->nullable()->after('profile_photo_path');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'qrcode')) {
                $table->dropColumn('qrcode');
            }
            if (Schema::hasColumn('users', 'profile_url')) {
                $table->dropColumn('profile_url');
            }
        });
    }
};
