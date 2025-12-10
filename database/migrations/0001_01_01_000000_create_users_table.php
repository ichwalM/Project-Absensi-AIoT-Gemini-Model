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
        // 1. KITA PAKAI 'CREATE', BUKAN 'TABLE'
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Wajib ada (Primary Key)
            $table->string('name'); // Wajib ada
            $table->string('email')->unique(); // Wajib ada
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Wajib ada
            
            // --- TAMBAHAN KITA (AIoT Features) ---
            $table->string('rfid_uid')->unique()->nullable(); // Untuk Tap Kartu
            $table->string('role')->default('asisten');       // Untuk Hak Akses
            // -------------------------------------

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};