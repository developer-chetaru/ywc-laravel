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
        Schema::table('yachts', function (Blueprint $table) {
            // Ownership & Management
            $table->string('owner_name')->nullable()->after('status');
            $table->enum('ownership_type', ['private', 'company', 'charter_management'])->nullable()->after('owner_name');
            $table->string('captain_name')->nullable()->after('ownership_type');
            $table->string('management_company')->nullable()->after('captain_name');
            $table->boolean('is_charter_available')->default(false)->after('management_company');
            $table->string('charter_rate')->nullable()->after('is_charter_available');
            
            // Enhanced crew information
            $table->integer('current_crew_size')->nullable()->after('crew_capacity');
            $table->text('crew_structure')->nullable()->after('current_crew_size');
            $table->text('rotation_schedule')->nullable()->after('crew_structure');
            
            // Rating breakdowns
            $table->decimal('yacht_quality_rating_avg', 3, 2)->nullable()->after('rating_avg');
            $table->decimal('crew_culture_rating_avg', 3, 2)->nullable()->after('yacht_quality_rating_avg');
            $table->decimal('management_rating_avg', 3, 2)->nullable()->after('crew_culture_rating_avg');
            $table->decimal('benefits_rating_avg', 3, 2)->nullable()->after('management_rating_avg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yachts', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name',
                'ownership_type',
                'captain_name',
                'management_company',
                'is_charter_available',
                'charter_rate',
                'current_crew_size',
                'crew_structure',
                'rotation_schedule',
                'yacht_quality_rating_avg',
                'crew_culture_rating_avg',
                'management_rating_avg',
                'benefits_rating_avg',
            ]);
        });
    }
};
