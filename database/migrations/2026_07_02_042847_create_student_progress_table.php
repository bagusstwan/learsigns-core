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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel users (siswa) dan tabel modules
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->integer('accuracy'); // Menyimpan skor akurasi tertinggi
            $table->boolean('is_completed')->default(false); // Status kelulusan modul
            $table->timestamps();
            
            // Memastikan seorang siswa hanya punya 1 baris record data untuk 1 modul tertentu
            $table->unique(['user_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
