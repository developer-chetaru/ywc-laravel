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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'professional_summary')) {
                $table->text('professional_summary')->nullable()->after('availability_message');
            }
            if (!Schema::hasColumn('users', 'current_position')) {
                $table->string('current_position')->nullable()->after('professional_summary');
            }
            if (!Schema::hasColumn('users', 'employment_type')) {
                $table->string('employment_type')->nullable()->after('current_position');
            }
            if (!Schema::hasColumn('users', 'expected_salary')) {
                $table->string('expected_salary')->nullable()->after('employment_type');
            }
            if (!Schema::hasColumn('users', 'vessel_preference')) {
                $table->string('vessel_preference')->nullable()->after('expected_salary');
            }
            if (!Schema::hasColumn('users', 'special_services')) {
                $table->string('special_services')->nullable()->after('vessel_preference');
            }
            if (!Schema::hasColumn('users', 'available_from')) {
                $table->date('available_from')->nullable()->after('special_services');
            }
            if (!Schema::hasColumn('users', 'passport_validity')) {
                $table->date('passport_validity')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('users', 'visas')) {
                $table->string('visas')->nullable()->after('passport_validity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'professional_summary',
                'current_position',
                'employment_type',
                'expected_salary',
                'vessel_preference',
                'special_services',
                'available_from',
                'passport_validity',
                'visas',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
