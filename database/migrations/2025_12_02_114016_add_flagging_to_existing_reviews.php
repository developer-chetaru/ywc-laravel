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
        // Add flagging fields to yacht_reviews
        if (Schema::hasTable('yacht_reviews') && !Schema::hasColumn('yacht_reviews', 'is_flagged')) {
            Schema::table('yacht_reviews', function (Blueprint $table) {
                $table->boolean('is_flagged')->default(false)->after('is_approved');
                $table->text('flag_reason')->nullable()->after('is_flagged');
            });
        }

        // Add flagging fields to marina_reviews
        if (Schema::hasTable('marina_reviews') && !Schema::hasColumn('marina_reviews', 'is_flagged')) {
            Schema::table('marina_reviews', function (Blueprint $table) {
                $table->boolean('is_flagged')->default(false)->after('is_approved');
                $table->text('flag_reason')->nullable()->after('is_flagged');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('yacht_reviews') && Schema::hasColumn('yacht_reviews', 'is_flagged')) {
            Schema::table('yacht_reviews', function (Blueprint $table) {
                $table->dropColumn(['is_flagged', 'flag_reason']);
            });
        }

        if (Schema::hasTable('marina_reviews') && Schema::hasColumn('marina_reviews', 'is_flagged')) {
            Schema::table('marina_reviews', function (Blueprint $table) {
                $table->dropColumn(['is_flagged', 'flag_reason']);
            });
        }
    }
};
