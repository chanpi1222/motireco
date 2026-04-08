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
        abort_unless($habit->user_id === auth()->id(), 403);
        // 今日の日付を取得（タイムゾーンはアプリ設定に依存）
        // → 日単位での記録管理の基準となる
        $today = Carbon::today();

        // 一連の処理（ログ操作 + XP更新）をトランザクションでまとめる
        // → 途中で失敗した場合にデータ不整合を防ぐ
        return DB::transaction(function () use ($request, $habit, $today) {

            // ログインユーザーを取得
            $user = auth()->user();

            // 指定習慣 × 今日の日付のログを取得
            // → datetime型でも日単位で判定できるよう whereDate を使用
            $log = HabitLog::where('habit_id', $habit->id)
                ->whereDate('date', $today)
                ->first();

            // 今日の完了件数を取得（後で増減させるための基準値）
            $todayCompletedCount = HabitLog::query()
                ->whereDate('date', $today)
                ->whereHas('habit', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->count();

            // ===============================
            // すでに記録がある場合（取り消し）
            // ===============================
            if ($log) {

                // 処理前のXPとレベルを保持（レベルダウン判定用）
                $beforeXp = (int)($user->xp ?? 0);
                $beforeLevel = $user->calcLevel($beforeXp);

                // ログ削除（= 今日の達成を取り消し）
                $log->delete();

                // XPを減算（最低0で止めるガード付き）
                $user->xp = max(0, $beforeXp - 10);
                $user->save();

                // 最新状態を再取得（DBとの同期）
                $user->refresh();

                // 処理後のXP・レベルを取得
                $afterXp = (int)$user->xp;
                $afterLevel = $user->calcLevel($afterXp);

                // 今日の完了件数を -1（0未満にならないようガード）
                $todayCompletedCount = max(0, $todayCompletedCount - 1);

                // ===== レベル進捗計算 =====
                // 現在レベルの開始XP
                $currentLevelTotalXp = $user->xpForLevel($afterLevel);

                // 次レベル到達に必要な累計XP
                $nextLevelTotalXp = $user->xpForLevel($afterLevel + 1);

                // 現在レベル内での進捗XP
                $currentLevelXp = max(0, $afterXp - $currentLevelTotalXp);

                // 次レベルまでに必要なXP量
                $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);

                // 進捗率（%表示用）
                $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

                // フロントへJSONで結果を返却（非同期UI更新用）
                return response()->json([
                    'done' => false, // 未達成状態に戻る
                    'message' => '今日の記録を取り消しました。',
                    'xp_delta' => -10, // XP変化量
                    'level_up' => null, // レベル変動なし
                    'today_completed_count' => $todayCompletedCount,
                    'total_xp' => $afterXp,
                    'level' => $afterLevel,
                    'current_level_xp' => $currentLevelXp,
                    'next_level_xp' => $nextLevelXp,
                    'xp_progress_percent' => $xpProgressPercent,
                    'streak_today' => 0,
                ]);
            }

            // ===============================
            // 記録がない場合（新規追加）
            // ===============================

            // 今日のログを作成
            // → startOfDay()で時刻ブレを防止（datetimeでも一意性担保）
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => $today->copy()->startOfDay(),
            ]);

            // 処理前のXP・レベルを取得
            $beforeXp = (int)($user->xp ?? 0);
            $beforeLevel = $user->calcLevel($beforeXp);

            // XPを+10（DB側で安全に加算）
            $user->increment('xp', 10);

            // 最新状態を取得
            $user->refresh();

            // 処理後のXP・レベル
            $afterXp = (int)$user->xp;
            $afterLevel = $user->calcLevel($afterXp);

            // レベルアップ判定
            $levelUpPayload = null;
            if ($afterLevel > $beforeLevel) {
                $levelUpPayload = [
                    'from' => $beforeLevel,
                    'to' => $afterLevel,
                ];
            }

            // 今日完了件数を +1
            $todayCompletedCount = $todayCompletedCount + 1;

            // ===== レベル進捗計算（上と同じロジック）=====
            $currentLevelTotalXp = $user->xpForLevel($afterLevel);
            $nextLevelTotalXp = $user->xpForLevel($afterLevel + 1);
            $currentLevelXp = max(0, $afterXp - $currentLevelTotalXp);
            $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);
            $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

            // フロントへ結果返却（UI更新用）
            return response()->json([
                'done' => true, // 達成状態
                'message' => '今日の記録を追加しました!',
                'xp_delta' => 10,
                'level_up' => $levelUpPayload,
                'today_completed_count' => $todayCompletedCount,
                'total_xp' => $afterXp,
                'level' => $afterLevel,
                'current_level_xp' => $currentLevelXp,
                'next_level_xp' => $nextLevelXp,
                'xp_progress_percent' => $xpProgressPercent,
                'streak_today' => 1,
            ]);
        });
    }
}
