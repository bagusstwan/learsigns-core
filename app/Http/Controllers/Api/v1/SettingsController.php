<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password'
        ]);

        $user = $request->user();

        // Verifikasi kata sandi lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Kata sandi lama tidak sesuai dengan sistem.'
            ], 400);
        }

        // Simpan kata sandi baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Kredensial keamanan berhasil diperbarui.'
        ], 200);
    }
}