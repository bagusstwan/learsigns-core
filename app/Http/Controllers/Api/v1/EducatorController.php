<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EducatorController extends Controller
{
    /**
     * 1. Mengambil Data Dashboard (Daftar Murid & Riwayat Tugas)
     */
    public function index(Request $request)
    {
        $teacher = $request->user();

        // Mengambil daftar murid HANYA dari instansi/kelas yang sama dengan guru
        $students = User::where('role', 'student')
                        ->where('institution', $teacher->institution)
                        ->select('id', 'name', 'institution as class')
                        ->get()
                        ->map(function($student) {
                            $words = explode(' ', $student->name);
                            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            return [
                                'id' => $student->id,
                                'name' => $student->name,
                                'class' => $student->class ?? 'Siswa',
                                'initials' => $initials
                            ];
                        });

        // Mengambil riwayat tugas beserta kolom stars_earned dan feedback
        $assignments = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('assignments')) {
            $assignments = DB::table('assignments')
                ->join('users', 'assignments.student_id', '=', 'users.id')
                ->where('assignments.teacher_id', $teacher->id)
                ->select('assignments.*', 'users.name as studentName')
                ->orderBy('assignments.created_at', 'desc')
                ->get()
                ->map(function($task) {
                    return [
                        'id' => $task->id,
                        'studentName' => $task->studentName,
                        'title' => $task->title,
                        'target' => $task->target,
                        'notes' => $task->notes,
                        'status' => $task->status,
                        'stars_earned' => $task->stars_earned, // Kolom baru
                        'feedback' => $task->feedback,         // Kolom baru
                        'date' => Carbon::parse($task->created_at)->locale('id')->translatedFormat('d M Y')
                    ];
                });
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'students' => $students,
                'assignments' => $assignments
            ]
        ], 200);
    }

    /**
     * 2. Mendelegasikan Tugas Baru ke Murid
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'target' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        DB::table('assignments')->insert([
            'student_id' => $request->student_id,
            'teacher_id' => $request->user()->id,
            'title' => $request->title,
            'target' => $request->target,
            'notes' => $request->notes,
            'status' => 'Belum Dikerjakan',
            'stars_earned' => 0,
            'feedback' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tugas berhasil didelegasikan.'
        ], 201);
    }

    /**
     * 3. Menambahkan Murid Baru (Fitur Manajemen Murid)
     */
    public function storeStudent(Request $request)
    {
        $teacher = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        try {
            $student = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'institution' => $teacher->institution,
                'stars' => 0
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Murid baru berhasil didaftarkan.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendaftarkan murid. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * 4. Memberikan Penilaian & Bintang (Evaluasi Guru)
     */
    public function evaluateTask(Request $request, $id)
    {
        $teacher = $request->user();

        $request->validate([
            'stars_earned' => 'required|integer|min:1|max:50', // Batas maksimal bintang yang bisa diberikan
            'feedback' => 'nullable|string'
        ]);

        // Cek apakah tugas dengan ID tersebut milik guru
        $assignment = DB::table('assignments')->where('id', $id)->where('teacher_id', $teacher->id)->first();

        if (!$assignment) {
            return response()->json(['status' => 'error', 'message' => 'Tugas tidak ditemukan.'], 404);
        }

        if ($assignment->status === 'Selesai Dinilai') {
            return response()->json(['status' => 'error', 'message' => 'Tugas ini sudah dinilai.'], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('assignments')->where('id', $id)->update([
                'status' => 'Selesai Dinilai',
                'stars_earned' => $request->stars_earned,
                'feedback' => $request->feedback,
                'updated_at' => now()
            ]);

            // Tambahkan bintang langsung ke total bintang milik murid tersebut
            User::where('id', $assignment->student_id)->increment('stars', $request->stars_earned);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tugas berhasil dinilai dan poin bintang telah dikirim ke murid!'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan penilaian.'], 500);
        }
    }
}