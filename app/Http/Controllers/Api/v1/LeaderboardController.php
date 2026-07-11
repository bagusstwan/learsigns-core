<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $currentUserId = $request->user()->id;

        // Ambil 50 siswa teratas berdasarkan jumlah bintang, kecuali user dengan ID 1 (admin)
        $users = User::where('role', 'student')
                     ->where('id', '!=', 1)
                     ->orderBy('stars', 'desc')
                     ->take(50)
                     ->get();

        $leaderboard = $users->map(function ($user, $index) use ($currentUserId) {
            // Generate Inisial Nama (Misal: "Bagus Setiawan" -> "BS")
            $words = explode(' ', $user->name);
            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

            // Tentukan Tier berdasarkan jumlah bintang
            $stars = $user->stars ?? 0;
            $tier = 'Bronze';
            if ($stars >= 1500) $tier = 'Diamond Elite';
            elseif ($stars >= 1200) $tier = 'Platinum Elite';
            elseif ($stars >= 800) $tier = 'Gold Pro';
            elseif ($stars >= 500) $tier = 'Gold';
            elseif ($stars >= 200) $tier = 'Silver';

            // Hitung total Quest & Win Rate dari database langsung (anti-error relasi)
            $questsCount = DB::table('user_quests')->where('user_id', $user->id)->count();
            $winRate = DB::table('student_progress')->where('user_id', $user->id)->avg('accuracy') ?? 0;

            return [
                'rank' => $index + 1,
                'name' => $user->name,
                'initials' => $initials,
                // Gunakan kolom institution, atau default jika kosong
                'class' => $user->institution ?? 'Siswa Viba.ai', 
                'stars' => $stars,
                'tier' => $tier,
                'quests' => $questsCount,
                'winRate' => round($winRate) . '%',
                'isMe' => $user->id === $currentUserId
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $leaderboard
        ], 200);
    }
}