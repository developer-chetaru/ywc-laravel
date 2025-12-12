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
            $table->json('mental_health_concerns')->nullable();
            $table->json('current_symptoms')->nullable();
            $table->boolean('previous_therapy_experience')->default(false);
            $table->text('previous_therapy_details')->nullable();
            $table->json('medication_information')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->json('preferred_therapist_characteristics')->nullable();
            $table->json('session_format_preferences')->nullable(); // ['video', 'voice', 'chat']
            $table->decimal('mental_health_credit_balance', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mental_health_concerns',
                'current_symptoms',
                'previous_therapy_experience',
                'previous_therapy_details',
                'medication_information',
                'emergency_contacts',
                'preferred_therapist_characteristics',
                'session_format_preferences',
                'mental_health_credit_balance',
            ]);
        });
    }
};
