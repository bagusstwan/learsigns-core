<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\API\v1\StudentProgressController;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\QuestController;
use App\Http\Controllers\API\v1\LeaderboardController;
use App\Http\Controllers\API\v1\ProfileController;
use App\Http\Controllers\API\v1\SettingsController;
use App\Http\Controllers\API\v1\EducatorController;

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

        // Leaderboard
        Route::get('/leaderboard', [\App\Http\Controllers\API\v1\LeaderboardController::class, 'index']);

        // Profil
        Route::get('/profile', [\App\Http\Controllers\API\v1\ProfileController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\API\v1\ProfileController::class, 'update']);

        // Pengaturan
        Route::put('/settings/password', [\App\Http\Controllers\API\v1\SettingsController::class, 'updatePassword']);
        
        // Educator
        Route::get('/educator/dashboard', [\App\Http\Controllers\API\v1\EducatorController::class, 'index']);
        Route::post('/educator/assignments', [\App\Http\Controllers\API\v1\EducatorController::class, 'store']);
        Route::post('/educator/assignments/{id}/evaluate', [\App\Http\Controllers\API\v1\EducatorController::class, 'evaluateTask']);
        Route::post('/educator/students', [\App\Http\Controllers\API\v1\EducatorController::class, 'storeStudent']);
    });
});