<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\DB;

class StudentProgressController extends Controller
{
    public function recordProgress(Request $request)
    {
        // Validasi input data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'module_id' => 'required|exists:modules,id',
            'accuracy' => 'required|integer|min:0|max:100',
        ]);

        // Mencatat progres belajar siswa menggunakan updateOrCreate
        $progress = StudentProgress::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'module_id' => $request->module_id,
            ],
            [
                // Menggunakan fungsi max agar jika skor baru lebih rendah, skor tertinggi lama tidak hilang (Tanpa backslash)
                'accuracy' => DB::raw("GREATEST(accuracy, {$request->accuracy})"),
                // Jika skor akurasi >= 90, maka status kelulusan modul diubah menjadi true, jika tidak tetap seperti sebelumnya
                'is_completed' => $request->accuracy >= 90 ? true : DB::raw('is_completed'),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Progres belajar siswa berhasil direkam.',
            'data' => $progress
        ], 200);
    }
}