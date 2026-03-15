<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HabitLogController extends Controller
{

    public function toggle(Request $request, Habit $habit)
    {
        $today = Carbon::today(); // JSTならJSTの今日

        return DB::transaction(function () use ($request, $habit, $today) {

            $user = auth()->user();

            // ✅ 「日」で検索（DBが datetime でも date でも拾える）
            $log = HabitLog::where('habit_id', $habit->id)
                ->whereDate('date', $today)
                ->first();

            // 現在の今日完了件数（あとで±する）
            $todayCompletedCount = HabitLog::whereDate('date', $today)->count();

            if ($log) {
                // ===== XP減算（DB的に安全なやり方）=====
                $beforeXp = (int)($user->xp ?? 0);
                $beforeLevel = $user->calcLevel($beforeXp);

                $log->delete();

                // XP -10（下限ガード）
                $user->xp = max(0, $beforeXp - 10);
                $user->save();
                $user->refresh();

                $afterXp = (int)$user->xp;
                $afterLevel = $user->calcLevel($afterXp);

                // 今日完了件数は -1
                $todayCompletedCount = max(0, $todayCompletedCount - 1);

                $currentLevelTotalXp = $user->xpForLevel($afterLevel);
                $nextLevelTotalXp = $user->xpForLevel($afterLevel + 1);
                $currentLevelXp = max(0, $afterXp - $currentLevelTotalXp);
                $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);
                $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

                return response()->json([
                    'done' => false,
                    'message' => '今日の記録を取り消しました。',
                    'xp_delta' => -10,
                    'level_up' => null,
                    'today_completed_count' => $todayCompletedCount,
                    'total_xp' => $afterXp,
                    'level' => $afterLevel,
                    'current_level_xp' => $currentLevelXp,
                    'next_level_xp' => $nextLevelXp,
                    'xp_progress_percent' => $xpProgressPercent,
                ]);
            }

            // ✅ 保存も「今日の00:00:00」に揃える（datetimeでもブレない）
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => $today->copy()->startOfDay(), // ★ここが重要
            ]);

            $beforeXp = (int)($user->xp ?? 0);
            $beforeLevel = $user->calcLevel($beforeXp);

            $user->increment('xp', 10);
            $user->refresh();

            $afterXp = (int)$user->xp;
            $afterLevel = $user->calcLevel($afterXp);

            $levelUpPayload = null;
            if ($afterLevel > $beforeLevel) {
                $levelUpPayload = [
                    'from' => $beforeLevel,
                    'to' => $afterLevel,
                ];
            }

            // 今日完了件数は +1
            $todayCompletedCount = $todayCompletedCount + 1;

            $currentLevelTotalXp = $user->xpForLevel($afterLevel);
            $nextLevelTotalXp = $user->xpForLevel($afterLevel + 1);
            $currentLevelXp = max(0, $afterXp - $currentLevelTotalXp);
            $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);
            $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

            return response()->json([
                'done' => true,
                'message' => '今日の記録を追加しました!',
                'xp_delta' => 10,
                'level_up' => $levelUpPayload,
                'today_completed_count' => $todayCompletedCount,
                'total_xp' => $afterXp,
                'level' => $afterLevel,
                'current_level_xp' => $currentLevelXp,
                'next_level_xp' => $nextLevelXp,
                'xp_progress_percent' => $xpProgressPercent,
            ]);
        });
    }
}
