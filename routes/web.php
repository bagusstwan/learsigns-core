<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/serve-ai/{folder}/{filename}', function ($folder, $filename) {
    // Membaca file dari folder public/ai-models/
    $path = public_path("ai-models/{$folder}/{$filename}");
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    // Mengirim file kembali ke React dengan header izin CORS penuh
    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
    ]);
});