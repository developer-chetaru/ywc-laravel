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
        Schema::table('document_shares', function (Blueprint $table) {
            // Granular Permissions
            $table->boolean('can_download')->default(true)->after('is_active');
            $table->boolean('can_print')->default(true)->after('can_download');
            $table->boolean('can_share')->default(false)->after('can_print');
            $table->boolean('can_comment')->default(false)->after('can_share');
            
            // Access Control
            $table->boolean('is_one_time')->default(false)->after('can_comment');
            $table->integer('max_views')->nullable()->after('is_one_time')->comment('Max number of views allowed');
            $table->integer('view_count')->default(0)->after('max_views');
            $table->string('password')->nullable()->after('view_count')->comment('Optional password protection');
            $table->boolean('require_watermark')->default(false)->after('password');
            
            // Time Restrictions
            $table->timestamp('access_start_date')->nullable()->after('require_watermark');
            $table->timestamp('access_end_date')->nullable()->after('access_start_date');
            
            // Metadata
            $table->string('share_type')->default('custom')->after('access_end_date')->comment('custom, template, wizard');
            $table->text('share_notes')->nullable()->after('share_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_shares', function (Blueprint $table) {
            //
        });
    }
};
