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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "Huruf A", "Kata IBU"
            $table->enum('level_type', ['abjad', 'kata', 'kalimat']); // Pemisah ke-3 level
            $table->string('target_gesture'); // Kunci deteksi untuk AI (Contoh: "A", "IBU")
            $table->text('description')->nullable(); // Instruksi untuk murid
            $table->string('reference_image')->nullable(); // Gambar referensi gestur
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
