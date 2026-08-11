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
     * Retrieve the educator's dashboard overview.
     * Includes the list of students within the same institution and assignment history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $teacher = $request->user();

        // Fetch students strictly bound to the educator's institution
        $students = User::where('role', 'student')
            ->where('institution', $teacher->institution)
            ->select('id', 'name', 'email', 'institution as class') // FIXED: Added 'email'
            ->get()
            ->map(function($student) {
                $words = explode(' ', $student->name);
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email, // FIXED: Now properly mapped to Frontend
                    'class' => $student->class ?? 'Siswa',
                    'initials' => $initials
                ];
            });

        $assignments = [];
        
        // Fetch assignment history including evaluation metrics
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
     * Delegate a new learning module/assignment to a specific student.
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
     * Register a new student credential bound to the educator's institution.
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
     * Update an existing student's credentials.
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
     * Remove a single student's access.
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
     * Perform a bulk deletion of selected students.
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
     * Evaluate an assignment and award stars to the student.
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

        // Verify ownership and existence
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

            // Increment the student's total stars based on evaluation
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
}