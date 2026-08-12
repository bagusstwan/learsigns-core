<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentProgressController extends Controller
{
    /** Function to record student learning progress */
    public function recordProgress(Request $request)
    {
        /** Retrieve the original User ID from the logged in token */
        $userId = $request->user()->id;

        /** Input validation process */
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'accuracy' => 'required|integer|min:0|max:100',
        ]);

        /** Save data using the logged in user ID */
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

    /** Realtime dashboard monitoring data fetcher */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $isEducator = in_array($user->role, ['teacher', 'corporate', 'educator']);

        if ($isEducator) {
            /** EDUCATOR METRICS Logic */
            $totalStars = DB::table('assignments')
                ->where('teacher_id', $user->id)
                ->where('status', 'Selesai Dinilai')
                ->sum('stars_earned');

            $avgStars = DB::table('assignments')
                ->where('teacher_id', $user->id)
                ->where('status', 'Selesai Dinilai')
                ->avg('stars_earned') ?? 0;
            $avgAccuracy = min(100, $avgStars * 2);

            $completedModules = DB::table('assignments')
                ->where('teacher_id', $user->id)
                ->where('status', 'Selesai Dinilai')
                ->count();

            $learningLogs = DB::table('assignments')
                ->join('users', 'assignments.student_id', 'users.id')
                ->where('assignments.teacher_id', $user->id)
                ->where('assignments.status', 'Selesai Dinilai')
                ->select('assignments.id', 'assignments.title as module', 'assignments.stars_earned', 'assignments.updated_at as date', 'users.name as studentName')
                ->orderBy('assignments.updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'module' => $log->module,
                        'studentName' => $log->studentName,
                        'score' => min(100, $log->stars_earned * 2),
                        'date' => Carbon::parse($log->date)->locale('id')->diffForHumans(),
                    ];
                });

            $questLogs = DB::table('assignments')
                ->join('users', 'assignments.student_id', 'users.id')
                ->where('assignments.teacher_id', $user->id)
                ->where('assignments.status', 'Selesai Dinilai')
                ->select('assignments.id', 'assignments.title as quest', 'users.name as studentName', 'assignments.stars_earned as reward')
                ->orderBy('assignments.updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => 'q_' . $log->id,
                        'quest' => $log->quest,
                        'studentName' => $log->studentName,
                        'reward' => $log->reward,
                        'date' => 'Berhasil Diselesaikan',
                    ];
                });

            $weeklyActivity = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                
                $dailyAssignments = DB::table('assignments')
                    ->join('users', 'assignments.student_id', 'users.id')
                    ->where('assignments.teacher_id', $user->id)
                    ->whereDate('assignments.updated_at', $date)
                    ->where('assignments.status', 'Selesai Dinilai')
                    ->select('users.name', 'assignments.stars_earned')
                    ->get();

                $activeStudents = $dailyAssignments->pluck('name')->unique()->values()->toArray();
                $avgDailyScore = $dailyAssignments->avg('stars_earned') ?? 0;

                $weeklyActivity[] = [
                    'day' => $date->locale('id')->isoFormat('ddd'),
                    'skor' => round(min(100, $avgDailyScore * 2)),
                    'active_students' => $activeStudents
                ];
            }

            $todayStarReceivers = DB::table('assignments')
                ->join('users', 'assignments.student_id', 'users.id')
                ->where('assignments.teacher_id', $user->id)
                ->whereDate('assignments.updated_at', Carbon::today())
                ->where('assignments.status', 'Selesai Dinilai')
                ->select('users.name', DB::raw('SUM(assignments.stars_earned) as stars'))
                ->groupBy('users.id', 'users.name')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_stars' => (int) $totalStars,
                    'average_accuracy' => round($avgAccuracy, 1),
                    'completed_modules' => $completedModules,
                    'learning_logs' => $learningLogs,
                    'weekly_activity' => $weeklyActivity,
                    'quest_logs' => $questLogs,
                    'today_star_receivers' => $todayStarReceivers
                ]
            ], 200);

        } else {
            /** STUDENT METRICS Logic Original */
            $userId = $user->id;
            $totalStars = $user->stars ?? 0;

            $avgAccuracy = DB::table('student_progress')
                ->where('user_id', $userId)
                ->avg('accuracy') ?? 0;

            $completedModules = DB::table('student_progress')
                ->where('user_id', $userId)
                ->where('is_completed', true)
                ->count();

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

            $weeklyActivity = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                
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
                    'quest_logs' => $questLogs,
                    'today_star_receivers' => [] 
                ]
            ], 200);
        }
    }
}