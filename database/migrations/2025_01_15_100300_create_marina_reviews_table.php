<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marina_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marina_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('review');
            $table->text('tips_tricks')->nullable();
            $table->tinyInteger('overall_rating')->unsigned();
            $table->tinyInteger('fuel_rating')->unsigned()->nullable();
            $table->tinyInteger('water_rating')->unsigned()->nullable();
            $table->tinyInteger('electricity_rating')->unsigned()->nullable();
            $table->tinyInteger('wifi_rating')->unsigned()->nullable();
            $table->tinyInteger('showers_rating')->unsigned()->nullable();
            $table->tinyInteger('laundry_rating')->unsigned()->nullable();
            $table->tinyInteger('maintenance_rating')->unsigned()->nullable();
            $table->tinyInteger('provisioning_rating')->unsigned()->nullable();
            $table->tinyInteger('staff_rating')->unsigned()->nullable();
            $table->tinyInteger('value_rating')->unsigned()->nullable();
            $table->tinyInteger('protection_rating')->unsigned()->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('visit_date')->nullable();
            $table->string('yacht_length_meters')->nullable();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['marina_id', 'is_approved']);
            $table->index(['user_id', 'is_anonymous']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marina_reviews');
    }
};

