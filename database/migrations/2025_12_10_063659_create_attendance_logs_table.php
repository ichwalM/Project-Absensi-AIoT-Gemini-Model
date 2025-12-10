<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamp('check_in_time')->useCurrent(); // Otomatis isi jam sekarang
            $table->timestamp('check_out_time')->nullable();  // Kosong pas check-in
            
            // Simpan path file rekaman suara (.wav)
            $table->string('audio_path')->nullable();
            
            // Hasil olahan AI
            $table->text('activity_summary')->nullable(); // Laporan Formal dari Gemini
            
            // Status: 'working' (sedang di lab), 'done' (sudah pulang)
            $table->enum('status', ['working', 'done'])->default('working');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
