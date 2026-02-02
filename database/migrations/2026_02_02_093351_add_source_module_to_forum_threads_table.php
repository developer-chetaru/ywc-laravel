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
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->string('source_module', 50)->nullable()->after('locked');
            $table->unsignedInteger('source_item_id')->nullable()->after('source_module');
            $table->string('source_item_type', 50)->nullable()->after('source_item_id');
            
            // Index for faster lookups
            $table->index(['source_module', 'source_item_id', 'source_item_type'], 'forum_threads_source_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropIndex('forum_threads_source_index');
            $table->dropColumn(['source_module', 'source_item_id', 'source_item_type']);
        });
    }
};
