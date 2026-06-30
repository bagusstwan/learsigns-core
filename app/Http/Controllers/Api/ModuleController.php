<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    /**
     * Mengambil semua daftar modul pembelajaran yang aktif
     */
    public function index(Request $request): JsonResponse
    {
        // Kita bisa menambahkan filter berdasarkan level_type jika diminta oleh React
        $query = Module::where('is_active', true);

        if ($request->has('level')) {
            $query->where('level_type', $request->level);
        }

        $modules = $query->orderBy('level_type', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data modul berhasil diambil',
            'data' => $modules
        ], 200);
    }

    /**
     * Mengambil detail satu modul beserta target gesturnya
     */
    public function show($id): JsonResponse
    {
        $module = Module::where('is_active', true)->find($id);

        if (!$module) {
            return response()->json([
                'status' => 'error',
                'message' => 'Modul tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail modul berhasil diambil',
            'data' => $module
        ], 200);
    }
}