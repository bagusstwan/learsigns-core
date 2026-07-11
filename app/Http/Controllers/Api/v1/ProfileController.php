<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileController extends Controller
{
    // Mengambil data profil dan statistik
    public function show(Request $request)
    {
        $user = $request->user();
        $stars = $user->stars ?? 0;

        // Hitung Ranking Global (Hanya role student, kecualikan Super Admin ID 1)
        $rank = User::where('role', 'student')
                    ->where('id', '!=', 1)
                    ->where('stars', '>', $stars)
                    ->count() + 1;

        // Kalkulasi Tier
        $tier = 'Bronze'; $nextTier = 200;
        if ($stars >= 1500) { $tier = 'Diamond Elite'; $nextTier = $stars; }
        elseif ($stars >= 1200) { $tier = 'Platinum Elite'; $nextTier = 1500; }
        elseif ($stars >= 800) { $tier = 'Gold Pro'; $nextTier = 1200; }
        elseif ($stars >= 500) { $tier = 'Gold'; $nextTier = 800; }
        elseif ($stars >= 200) { $tier = 'Silver'; $nextTier = 500; }

        // Kalkulasi Win Rate & Quest
        $questsCount = DB::table('user_quests')->where('user_id', $user->id)->count();
        $winRate = DB::table('student_progress')->where('user_id', $user->id)->avg('accuracy') ?? 0;

        // Teks Peran Utama
        $roleText = 'Siswa Viba.ai';
        if ($user->role === 'teacher') $roleText = 'Instruktur / Guru';
        if ($user->role === 'admin' || $user->id === 1) $roleText = 'Super Administrator';

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $roleText,
                'institution' => $user->institution ?? '',
                'location' => 'Medan, Sumatera Utara', // Hardcode sementara, bisa ditambahkan ke DB nanti
                'joinDate' => Carbon::parse($user->created_at)->locale('id')->translatedFormat('F Y'),
                'rank' => $rank,
                'totalStars' => $stars,
                'tier' => $tier,
                'nextTierStars' => $nextTier,
                'quests' => $questsCount,
                'winRate' => round($winRate) . '%'
            ]
        ], 200);
    }

    // Menyimpan pembaruan profil (Nama dan Institusi)
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
        ]);

        $user->name = $request->name;
        $user->institution = $request->institution;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.'
        ], 200);
    }
}