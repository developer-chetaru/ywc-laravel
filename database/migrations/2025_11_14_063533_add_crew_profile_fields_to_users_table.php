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
            // Crew experience (position/role is handled by roles table)
            $table->integer('years_experience')->nullable();
            $table->string('current_yacht')->nullable();
            $table->text('languages')->nullable()->comment('JSON array of languages');
            $table->text('certifications')->nullable()->comment('JSON array of certifications');
            $table->text('specializations')->nullable()->comment('JSON array of specializations');
            $table->text('interests')->nullable()->comment('JSON array of interests/hobbies');
            
            // Availability and status
            $table->enum('availability_status', ['available', 'busy', 'looking_for_work', 'on_leave'])->nullable();
            $table->text('availability_message')->nullable();
            $table->boolean('looking_to_meet')->default(false);
            $table->boolean('looking_for_work')->default(false);
            
            // Professional details
            $table->integer('sea_service_time_months')->nullable();
            $table->text('previous_yachts')->nullable()->comment('JSON array of previous yachts');
            $table->decimal('rating', 3, 2)->nullable()->default(0)->comment('Average rating from reviews');
            $table->integer('total_reviews')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'years_experience',
                'current_yacht',
                'languages',
                'certifications',
                'specializations',
                'interests',
                'availability_status',
                'availability_message',
                'looking_to_meet',
                'looking_for_work',
                'sea_service_time_months',
                'previous_yachts',
                'rating',
                'total_reviews',
            ]);
        });
    }
};
