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
        Schema::table('yacht_galleries', function (Blueprint $table) {
            // Change category from enum to string to support any category value
            $table->string('category', 50)->default('other')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yacht_galleries', function (Blueprint $table) {
            // Revert back to enum if needed
            $table->enum('category', ['exterior', 'interior', 'crew_areas', 'deck', 'engine_room', 'bridge', 'crew_mess', 'crew_cabins', 'other'])->default('other')->change();
        });
    }
};
