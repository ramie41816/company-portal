<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create user_infos table first so users can reference it
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->timestamps();
        });

        // 2. Create users table without the redundant 'name' column
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('access_level')->default('staff'); // RBAC level (admin, manager, staff)
            $table->string('status')->default('inactive'); // active or inactive
            $table->foreignId('info_id')->nullable()->constrained('user_infos')->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed default admin user profile info first
        $infoId = DB::table('user_infos')->insertGetId([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed default admin user linked to user_info
        DB::table('users')->insert([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'access_level' => 'admin',
            'status' => 'active',
            'info_id' => $infoId,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_infos');
    }
};
