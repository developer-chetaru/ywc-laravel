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
        Schema::create('forum_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // new_reply, new_thread, quote, reaction, best_answer, pm, moderation
            $table->boolean('email_enabled')->default(true);
            $table->boolean('on_site_enabled')->default(true);
            $table->string('digest_mode')->default('none'); // none, daily, weekly
            $table->timestamps();

            $table->unique(['user_id', 'type']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_notification_preferences');
    }
};
