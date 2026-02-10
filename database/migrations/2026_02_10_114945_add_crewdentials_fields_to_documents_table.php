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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('crewdentials_document_id')->nullable()->after('status')->comment('Document ID in Crewdentials system');
            $table->text('crewdentials_verification_data')->nullable()->after('crewdentials_document_id')->comment('Full verification result JSON from Crewdentials');
            $table->boolean('imported_from_crewdentials')->default(false)->after('crewdentials_verification_data');
            $table->timestamp('crewdentials_verified_at')->nullable()->after('imported_from_crewdentials');
            
            $table->index('crewdentials_document_id');
            $table->index('imported_from_crewdentials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            //
        });
    }
};
