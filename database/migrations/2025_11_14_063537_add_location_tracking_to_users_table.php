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
            // Location tracking
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable()->comment('City, Marina, etc.');
            $table->timestamp('location_updated_at')->nullable();
            
            // Location privacy settings
            $table->enum('location_privacy', ['exact', 'approximate', 'city_only', 'hidden'])->default('approximate');
            $table->boolean('share_location')->default(false);
            $table->boolean('auto_hide_at_sea')->default(true);
            
            // Online status
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            
            // Visibility settings
            $table->enum('visibility', ['everyone', 'connections_only', 'verified_only', 'invisible'])->default('everyone');
            $table->boolean('show_in_discovery')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'location_name',
                'location_updated_at',
                'location_privacy',
                'share_location',
                'auto_hide_at_sea',
                'is_online',
                'last_seen_at',
                'visibility',
                'show_in_discovery',
            ]);
        });
    }
};
