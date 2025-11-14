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
        Schema::create('user_endorsements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('User being endorsed');
            $table->foreignId('endorser_id')->constrained('users')->cascadeOnDelete()->comment('User giving endorsement');
            $table->string('skill')->comment('Skill being endorsed');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'endorser_id', 'skill']);
            $table->index('user_id');
            $table->index('skill');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_endorsements');
    }
};
