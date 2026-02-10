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
            // QR Code path for document shares
            if (!Schema::hasColumn('document_shares', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('password_hash');
            }
            
            // Email restriction - restrict access to specific email only
            if (!Schema::hasColumn('document_shares', 'restrict_to_email')) {
                $table->string('restrict_to_email')->nullable()->after('recipient_email')
                    ->comment('If set, only this email can access the share');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_shares', function (Blueprint $table) {
            if (Schema::hasColumn('document_shares', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
            if (Schema::hasColumn('document_shares', 'restrict_to_email')) {
                $table->dropColumn('restrict_to_email');
            }
        });
    }
};
