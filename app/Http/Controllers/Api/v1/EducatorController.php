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
     * Retrieve the educator dashboard overview
     * Includes the list of students within the same institution and assignment history
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $teacher = $request->user();

        $students = User::where('role', 'student')
            ->where('institution', $teacher->institution)
            ->select('id', 'name', 'email', 'institution as class')
            ->get()
            ->map(function($student) {
                $words = explode(' ', $student->name);
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'class' => $student->class ?? 'Siswa',
                    'initials' => $initials
                ];
            });

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
                        'stars_earned' => $task->stars_earned,
                        'feedback' => $task->feedback,
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
     * Retrieve the summary data for the educator dashboard
     * Evaluates statistics active learning modules and pending evaluations cleanly
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardSummary(Request $request)
    {
        try {
            $teacher = $request->user();

            $activeStudents = User::where('role', 'student')
                ->where('institution', $teacher->institution)
                ->count();

            $completedModules = 0;
            $pendingEvaluations = 0;
            $pendingStudents = [];

            if (\Illuminate\Support\Facades\Schema::hasTable('assignments')) {
                $completedModules = DB::table('assignments')
                    ->where('teacher_id', $teacher->id)
                    ->whereIn('status', ['Selesai Dinilai', 'Selesai', 'Evaluated'])
                    ->count();

                $pendingEvaluations = DB::table('assignments')
                    ->where('teacher_id', $teacher->id)
                    ->whereIn('status', ['Menunggu Penilaian', 'Belum Dinilai', 'Pending', 'Belum Dikerjakan'])
                    ->count();

                $pendingStudents = DB::table('assignments')
                    ->join('users', 'assignments.student_id', '=', 'users.id')
                    ->where('assignments.teacher_id', $teacher->id)
                    ->select(
                        'assignments.id',
                        'users.name',
                        'users.email',
                        'assignments.title as active_module',
                        'assignments.target', // Ditambahkan agar modal frontend bisa baca target akurasi
                        'assignments.notes',  // Ditambahkan agar modal frontend bisa baca catatan pendidik
                        'assignments.stars_earned', // Alias 'as accuracy' dihapus murni menjadi stars_earned
                        'assignments.status'
                    )
                    ->orderBy('assignments.created_at', 'desc')
                    ->take(20)
                    ->get();
                    // map() yang menambahkan '%' dihapus total agar tidak merusak format angka bintang
            }

            $activeModules = [
                [
                    'level' => 'Modul Dasar',
                    'title' => 'Pengenalan Abjad SIBI',
                    'tags' => ['Pemula', 'Wajib'],
                    'desc' => 'Pantau tingkat akurasi siswa dalam memperagakan gestur tangan abjad A Z menggunakan deteksi sensor AI.',
                    'level_key' => 'huruf'
                ],
                [
                    'level' => 'Modul Menengah',
                    'title' => 'Pembentukan Kosa Kata',
                    'tags' => ['Menengah', 'Lanjutan'],
                    'desc' => 'Evaluasi kemampuan siswa dalam merangkai gestur menjadi sebuah kosa kata yang memiliki makna.',
                    'level_key' => 'kata'
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'stats' => [
                        'active_students' => $activeStudents,
                        'completed_modules' => $completedModules,
                        'pending_evaluations' => $pendingEvaluations
                    ],
                    'active_modules' => $activeModules,
                    'pending_students' => $pendingStudents
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Query Exception ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delegate a new learning module to a specific student
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
            'message' => 'Assignment delegated successfully.'
        ], 201);
    }

    /**
     * Register a new student credential bound to the educator institution
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'institution' => $teacher->institution,
                'stars' => 0
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Student credential registered successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register student credential.'
            ], 500);
        }
    }

    /**
     * Update an existing student credentials
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStudent(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6'
        ]);

        try {
            $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $student->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Student profile updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update student data.'], 500);
        }
    }

    /**
     * Remove a single student access
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyStudent($id)
    {
        try {
            $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
            $student->delete();

            return response()->json(['status' => 'success', 'message' => 'Student access removed.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to remove student.'], 500);
        }
    }

    /**
     * Perform a bulk deletion of selected students
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDestroyStudents(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        try {
            User::whereIn('id', $request->ids)->where('role', 'student')->delete();

            return response()->json(['status' => 'success', 'message' => 'Selected students have been removed.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to execute bulk deletion.'], 500);
        }
    }

    /**
     * Evaluate an assignment and award stars to the student
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function evaluateTask(Request $request, $id)
    {
        $teacher = $request->user();

        $request->validate([
            'stars_earned' => 'required|integer|min:1|max:50',
            'feedback' => 'nullable|string'
        ]);

        $assignment = DB::table('assignments')->where('id', $id)->where('teacher_id', $teacher->id)->first();

        if (!$assignment) {
            return response()->json(['status' => 'error', 'message' => 'Assignment not found.'], 404);
        }

        if (strtolower($assignment->status) === 'selesai dinilai' || strtolower($assignment->status) === 'evaluated') {
            return response()->json(['status' => 'error', 'message' => 'Assignment has already been evaluated.'], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('assignments')->where('id', $id)->update([
                'status' => 'Selesai Dinilai',
                'stars_earned' => $request->stars_earned,
                'feedback' => $request->feedback,
                'updated_at' => now()
            ]);

            User::where('id', $assignment->student_id)->increment('stars', $request->stars_earned);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Evaluation submitted and stars awarded successfully.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to save evaluation.'], 500);
        }
    }

    /**
     * Submit a live practical evaluation directly without prior assignment
     * Records the performance and awards stars to the student instantly
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function liveEvaluate(Request $request)
    {
        $teacher = $request->user();

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'module_id' => 'required',
            'stars_earned' => 'required|integer|min:1|max:50',
            'accuracy' => 'required'
        ]);

        DB::beginTransaction();
        try {
            DB::table('assignments')->insert([
                'student_id' => $request->student_id,
                'teacher_id' => $teacher->id,
                'title' => 'Evaluasi Praktikum Kelas',
                'target' => 'Tingkat Akurasi AI ' . $request->accuracy . ' Persen',
                'notes' => 'Praktikum ini dievaluasi secara langsung melalui sistem sensor AI di kelas.',
                'status' => 'Selesai Dinilai',
                'stars_earned' => $request->stars_earned,
                'feedback' => 'Telah dikonfirmasi oleh Pendidik.',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            User::where('id', $request->student_id)->increment('stars', $request->stars_earned);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Evaluasi praktikum berhasil direkam ke dalam peladen.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Kegagalan sistem saat menyimpan evaluasi praktikum.'
            ], 500);
        }
    }
}