<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yacht_management_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yacht_id')->constrained()->cascadeOnDelete();
            $table->foreignId('yacht_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Captain/management user
            $table->text('response');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index(['yacht_review_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yacht_management_responses');
    }
};

