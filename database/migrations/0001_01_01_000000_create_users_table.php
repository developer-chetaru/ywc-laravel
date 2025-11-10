<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Custom name fields instead of single 'name'
            $table->string('first_name');
            $table->string('last_name');

            // Jetstream/Fortify email + authentication fields
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Jetstream 2FA columns
            
            // Additional fields you wanted
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('birth_country')->nullable();
            $table->string('birth_province')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(false);

            // Jetstream team feature compatibility
            $table->foreignId('current_team_id')->nullable();

            // Remember token & timestamps
            $table->rememberToken();
            $table->timestamps();
        });
          Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
