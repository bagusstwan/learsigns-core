<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentProgressController extends Controller
{
    // 1. FUNGSI UNTUK MENYIMPAN PROGRESS
    public function recordProgress(Request $request)
    {
        // 1. Ambil ID User asli secara otomatis dari Token yang sedang login
        $userId = $request->user()->id;

        // 2. Validasi input
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'accuracy' => 'required|integer|min:0|max:100',
        ]);

        // 3. Simpan data menggunakan ID user yang login
        $progress = StudentProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'module_id' => $request->module_id,
            ],
            [
                'accuracy' => DB::raw("GREATEST(accuracy, {$request->accuracy})"),
                'is_completed' => $request->accuracy >= 90 ? true : DB::raw('is_completed'),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Progres belajar siswa berhasil direkam.',
            'data' => $progress
        ], 200);
    }

    // 2. FUNGSI DASHBOARD MONITORING (FULL REAL-TIME DATA)
    public function getStats(Request $request)
    {
        $userId = $request->user()->id;
        $totalStars = $request->user()->stars ?? 0;

        // A. Hitung Rata-rata Akurasi
        $avgAccuracy = DB::table('student_progress')
            ->where('user_id', $userId)
            ->avg('accuracy') ?? 0;

        // B. Hitung Total Modul Selesai
        $completedModules = DB::table('student_progress')
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->count();

        // C. Ambil 5 Log Pembelajaran Terakhir
        $learningLogs = DB::table('student_progress')
            ->join('modules', 'student_progress.module_id', '=', 'modules.id')
            ->where('student_progress.user_id', $userId)
            ->select('student_progress.id', 'modules.title as module', 'student_progress.accuracy as score', 'student_progress.updated_at as date')
            ->orderBy('student_progress.updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'module' => $log->module,
                    'score' => $log->score,
                    'date' => Carbon::parse($log->date)->locale('id')->diffForHumans(),
                ];
            });

        // D. Ambil 5 Riwayat Quest Terakhir
        $questLogs = DB::table('user_quests')
            ->join('quests', 'user_quests.quest_id', '=', 'quests.id')
            ->where('user_quests.user_id', $userId)
            ->select('user_quests.id', 'quests.title as quest', 'quests.reward_stars as reward')
            ->orderBy('user_quests.id', 'desc') 
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'quest' => $log->quest,
                    'reward' => $log->reward,
                    'date' => 'Berhasil Diselesaikan',
                ];
            });

        // E. Kalkulasi Data Grafik Aktivitas Mingguan
        $weeklyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Ambil rata-rata skor pada hari tersebut
            $dailyScore = DB::table('student_progress')
                ->where('user_id', $userId)
                ->whereDate('updated_at', $date)
                ->avg('accuracy') ?? 0;

            $weeklyActivity[] = [
                'day' => $date->locale('id')->isoFormat('ddd'),
                'skor' => round($dailyScore)
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_stars' => $totalStars,
                'average_accuracy' => round($avgAccuracy, 1),
                'completed_modules' => $completedModules,
                'learning_logs' => $learningLogs,
                'weekly_activity' => $weeklyActivity,
                'quest_logs' => $questLogs
            ]
        ], 200);
    }
}