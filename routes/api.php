<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\API\v1\StudentProgressController;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\QuestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // ENDPOINT PUBLIK
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{id}', [ModuleController::class, 'show']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // ENDPOINT TERPROTEKSI
    Route::middleware('auth:sanctum')->group(function () {
        
        // Ambil data user saat ini
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Autentikasi & Progress
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/progress', [StudentProgressController::class, 'recordProgress']);
        Route::get('/progress/stats', [StudentProgressController::class, 'getStats']);
        
        // Quest (Misi Harian)
        Route::get('/quests', [QuestController::class, 'index']);
        Route::post('/quests/complete', [QuestController::class, 'complete']);
        
    });
});