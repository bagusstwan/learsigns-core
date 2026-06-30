<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dataset_records', function (Blueprint $table) {
            $table->id();
            $table->string('label')->index(); // Contoh: 'A', 'B', 'HALO'
            $table->enum('gesture_type', ['static', 'dynamic'])->default('static'); 
            $table->json('landmarks'); // Tempat menampung array matriks koordinat MediaPipe
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dataset_records');
    }
};