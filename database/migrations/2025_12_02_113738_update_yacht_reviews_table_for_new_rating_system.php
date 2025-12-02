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
        Schema::table('yacht_reviews', function (Blueprint $table) {
            // Add new 5-category rating system fields
            $table->tinyInteger('yacht_quality_rating')->unsigned()->nullable()->after('overall_rating');
            $table->tinyInteger('crew_culture_rating')->unsigned()->nullable()->after('yacht_quality_rating');
            $table->tinyInteger('benefits_rating')->unsigned()->nullable()->after('crew_culture_rating');
            
            // Keep existing fields for backward compatibility but mark as nullable
            // management_rating already exists
            // overall_rating already exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yacht_reviews', function (Blueprint $table) {
            $table->dropColumn(['yacht_quality_rating', 'crew_culture_rating', 'benefits_rating']);
        });
    }
};
