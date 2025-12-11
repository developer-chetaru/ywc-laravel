<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_advisors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->json('qualifications')->nullable();
            $table->json('specializations')->nullable();
            $table->json('languages')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->json('availability')->nullable(); // Working hours, timezone
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_consultations')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_advisors');
    }
};
