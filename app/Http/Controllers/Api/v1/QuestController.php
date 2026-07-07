<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quest;
use App\Models\UserQuest;
use Illuminate\Support\Facades\Auth;

class QuestController extends Controller
{
    // Mengambil daftar misi beserta status apakah user sudah menyelesaikannya
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $quests = Quest::where('is_active', true)->get()->map(function ($quest) use ($userId) {
            $isCompleted = UserQuest::where('user_id', $userId)
                                    ->where('quest_id', $quest->id)
                                    ->exists();
            
            return [
                'id' => $quest->id,
                'title' => $quest->title,
                'description' => $quest->description,
                'target_gesture' => $quest->target_gesture,
                'reward_stars' => $quest->reward_stars,
                'is_completed' => $isCompleted,
            ];
        });

        return response()->json([
            'status' => 'success',
            'user_stars' => $request->user()->stars,
            'data' => $quests
        ]);
    }

    // Memproses penyelesaian misi dan memberikan bintang
    public function complete(Request $request)
    {
        $request->validate([
            'quest_id' => 'required|exists:quests,id'
        ]);

        $user = $request->user();
        $quest = Quest::find($request->quest_id);

        // Cek apakah sudah pernah diselesaikan
        $alreadyCompleted = UserQuest::where('user_id', $user->id)
                                     ->where('quest_id', $quest->id)
                                     ->exists();

        if ($alreadyCompleted) {
            return response()->json(['status' => 'error', 'message' => 'Misi ini sudah diselesaikan sebelumnya.'], 400);
        }

        // Catat misi selesai
        UserQuest::create([
            'user_id' => $user->id,
            'quest_id' => $quest->id,
        ]);

        // Tambahkan bintang ke user
        $user->stars += $quest->reward_stars;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Misi berhasil diselesaikan! Bintang ditambahkan.',
            'new_total_stars' => $user->stars
        ]);
    }
}