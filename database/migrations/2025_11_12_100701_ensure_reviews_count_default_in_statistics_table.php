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
        // Only run if table exists (for existing installations)
        if (!Schema::hasTable('itinerary_route_statistics')) {
            return;
        }

        // Use raw SQL to ensure defaults are set (works without doctrine/dbal)
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `itinerary_route_statistics` 
            MODIFY COLUMN `views_total` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            MODIFY COLUMN `views_unique` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            MODIFY COLUMN `copies_total` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            MODIFY COLUMN `reviews_count` INT UNSIGNED NOT NULL DEFAULT 0,
            MODIFY COLUMN `rating_avg` DECIMAL(3,2) NOT NULL DEFAULT 0,
            MODIFY COLUMN `favorites_count` INT UNSIGNED NOT NULL DEFAULT 0,
            MODIFY COLUMN `shares_count` INT UNSIGNED NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - we're just ensuring defaults exist
    }
};
