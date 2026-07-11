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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('target');
            $table->text('notes')->nullable();
            
            // Tambahkan kolom baru untuk status, jumlah bintang yang didapat, dan catatan evaluasi
            $table->string('status')->default('Belum Dikerjakan'); 
            $table->integer('stars_earned')->default(0); // Jumlah bintang yang didapat
            $table->text('feedback')->nullable(); // Catatan evaluasi dari guru
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};