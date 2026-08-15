<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Memproses pendaftaran kredensial baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:student,teacher,corporate',
            'phone' => 'nullable|string|max:20',
            'institution' => 'nullable|string|max:255',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Enkripsi Sandi Searah
                'role' => $request->role,
                'phone' => $request->phone,
                'institution' => $request->institution,
            ]);

            $token = $user->createToken('viba-auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pendaftaran. Silakan coba beberapa saat lagi.'
            ], 500);
        }
    }

    /**
     * Memvalidasi sesi masuk dengan Proteksi Brute-Force
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // SECURE 1: Membuat kunci unik berdasarkan Email + Alamat IP Pengguna
        $throttleKey = Str::transliterate(Str::lower($request->email).'|'.$request->ip());

        // SECURE 2: Mengecek apakah pengguna sudah melewati batas maksimal percobaan (5 kali)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status' => 'error',
                'message' => 'Terlalu banyak percobaan. Akses ditangguhkan sementara. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429); // 429 Too Many Requests
        }

        $user = User::where('email', $request->email)->first();

        // SECURE 3: Validasi Kredensial & Pesan Error Seragam (Anti-Enumeration)
        if (! $user || ! Hash::check($request->password, $user->password)) {
            
            // Mencatat (Hit) kegagalan login ke dalam Rate Limiter
            RateLimiter::hit($throttleKey); 

            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial tidak valid. Silakan periksa kembali akses Anda.'
            ], 401); // 401 Unauthorized
        }

        // SECURE 4: Membersihkan riwayat kegagalan jika otentikasi berhasil
        RateLimiter::clear($throttleKey);

        $token = $user->createToken('viba-auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Menghancurkan token otorisasi saat ini
     */
    public function logout(Request $request)
    {
        try {
            // SECURE 5: Revoke/Hancurkan token secara absolut dari database
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Sesi diakhiri dengan aman.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengakhiri sesi.'
            ], 500);
        }
    }
}