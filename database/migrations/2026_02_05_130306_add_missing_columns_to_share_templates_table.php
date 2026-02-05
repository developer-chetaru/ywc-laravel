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
        Schema::table('share_templates', function (Blueprint $table) {
            // Permission columns
            $table->boolean('can_download')->default(true)->after('description');
            $table->boolean('can_print')->default(true)->after('can_download');
            $table->boolean('can_share')->default(false)->after('can_print');
            $table->boolean('can_comment')->default(false)->after('can_share');
            
            // Access control columns
            $table->boolean('is_one_time')->default(false)->after('can_comment');
            $table->integer('max_views')->nullable()->after('is_one_time');
            $table->boolean('require_password')->default(false)->after('max_views');
            $table->boolean('require_watermark')->default(false)->after('require_password');
            
            // Time settings columns
            $table->integer('duration_days')->nullable()->after('require_watermark');
            $table->boolean('has_access_window')->default(false)->after('duration_days');
            
            // Usage tracking
            $table->integer('usage_count')->default(0)->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('share_templates', function (Blueprint $table) {
            $table->dropColumn([
                'can_download',
                'can_print',
                'can_share',
                'can_comment',
                'is_one_time',
                'max_views',
                'require_password',
                'require_watermark',
                'duration_days',
                'has_access_window',
                'usage_count',
            ]);
        });
    }
};
