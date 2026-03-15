<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $today = today();
        $now = Carbon::now();

        // ✅ 習慣一覧（今日完了済みフラグ + 直近ログを一括取得）
        $habits = Habit::orderByDesc('created_at')
            ->withExists(['logs as is_done_today' => function ($q) use ($today) {
                $q->whereDate('date', $today);
            }])
            ->with(['logs' => function ($q) use ($today) {
                $q->select('id', 'habit_id', 'date')
                    ->whereDate('date', '>=', $today->copy()->subDays(60))
                    ->orderByDesc('date');
            }])
            ->get();

        // ✅ 習慣ごとのストリーク（今日やってなければ0）
        $habits = $habits->map(function ($habit) use ($today) {

            // logsの日付を YYYY-MM-DD に統一して重複排除
            $dates = $habit->logs
                ->map(fn($log) => Carbon::parse($log->date)->toDateString())
                ->unique()
                ->values()
                ->toArray();

            $set = array_flip($dates);

            $streak = 0;
            $cursor = $today->toDateString();

            while (isset($set[$cursor])) {
                $streak++;
                $cursor = Carbon::parse($cursor)->subDay()->toDateString();
            }

            // Bladeから呼べるようにプロパティ追加（DB保存はしない）
            $habit->streak_today = $streak;

            $habit->habit_title = match (true) {
                $streak >= 30 => '🏆 マスター',
                $streak >= 14 => '🔥 継続上級者',
                $streak >= 7  => '⭐ 1週間達成',
                $streak >= 3  => '💪 3日継続',
                $streak >= 1  => '🌱 スタート',
                default       => null,
            };

            return $habit;
        });

        $pendingHabits = $habits->filter(fn($h) => !$h->is_done_today);
        $doneHabits = $habits->filter(fn($h) => $h->is_done_today);

        // ✅ 今日の完了件数（今日のログ件数）
        $todayCompletedCount = HabitLog::whereDate('date', $today)->count();

        // ✅ XP（v1: 集計で算出）
        $xpPerDone = 10;

        $user = auth()->user();

        $totalXp = (int) ($user->xp ?? 0);

        // ✅ レベル計算：User::calcLevel に統一
        $level = $user->calcLevel($totalXp);

        // ✅ ゲージ計算：今のレベル帯の開始累計XP / 次レベル到達に必要な累計XP
        $currentLevelTotalXp = $user->xpForLevel($level);  // Lv開始の累計XP
        $nextLevelTotalXp = $user->xpForLevel($level + 1); // 次Lvの累計XP

        $currentLevelXp = max(0, $totalXp - $currentLevelTotalXp);        // このレベル帯で貯めたXP
        $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);  // このレベル帯で必要なXP（0除算防止）
        $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

        // 今日の獲得XP
        $todayXp = $todayCompletedCount * $xpPerDone;

        // 今月のXP（ログ件数ベース）
        $monthlyDoneCount = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->count();

        $monthlyXp = $monthlyDoneCount * $xpPerDone;

        // ✅ 今月の完了日数（その日に1件でもログがあった日を数える）
        $monthlyCompletedDays = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->select('date(date) as d') // 日付部分だけ
            ->distinct()
            ->count('d');

        $daySoFar = now()->day; // 今月の経過日数

        $monthlyRate = $daySoFar > 0 ? round(($monthlyCompletedDays / $daySoFar) * 100) : 0;

        // ✅ 今月の達成日（YYYY-MM-DD の配列）
        $activeDays = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw("date(date) as d")
            ->distinct()
            ->pluck('d')
            ->toArray();

        $activeDaySet = array_flip($activeDays); // Bladeで高速判定用

        // ✅ 全体ストリーク（あなたが入れたものはそのままでOK）
        // ... $globalStreak 計算はここにそのまま残す

        $activeDates = HabitLog::query()
            ->selectRaw("date(date) as d") // datetimeでも日付だけにする（SQLiteでもOK）
            ->whereDate('date', '>=', $today->copy()->subDays(60))
            ->distinct()
            ->pluck('d')
            ->toArray();

        $globalStreak = 0;
        $set = array_flip($activeDates);

        $cursor = $today->toDateString();
        while (isset($set[$cursor])) {
            $globalStreak++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        // ✅ 称号（全体ストリーク基準）
        $title = match (true) {
            $globalStreak >= 60 => '🏆 習慣レジェンド',
            $globalStreak >= 30 => '🌟 1ヶ月達成',
            $globalStreak >= 14 => '🔥 2週間マスター',
            $globalStreak >= 7 => '✅ 1週間継続',
            $globalStreak >= 3 => '💪 3日坊主卒業',
            $globalStreak >= 1 => '🚶 はじめの一歩',
            default            => '🧘 休憩中',
        };

        // ===== ストリーク途切れチェック =====
        $yesterday = Carbon::yesterday()->toDateString();

        $didYesterday = \DB::table('habit_logs')
            ->where('user_id', auth()->id())
            ->whereDate('created_at', $yesterday)
            ->exists();

        $streakBroken = !$didYesterday && $globalStreak === 0;

        // ===== 週間グラフ（直近7日）=====
        $days = 7;

        $from = Carbon::today()->subDays($days - 1);
        $to   = Carbon::today();

        $weeklyRows = HabitLog::query()
            ->whereBetween('date', [$from->startOfDay(), $to->endOfDay()])
            ->selectRaw("date(date) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        // 欠けている日を 0 で埋める（必須）
        $weeklyLabels = [];
        $weeklyCounts = [];

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $weeklyLabels[] = $d->format('m/d');
            $weeklyCounts[] = (int) ($weeklyRows[$key] ?? 0);
        }

        return view('dashboard', compact(
            'pendingHabits',
            'doneHabits',
            'todayCompletedCount',
            'monthlyCompletedDays',
            'globalStreak',
            'title',
            'todayXp',
            'xpPerDone',
            'level',
            'currentLevelXp',
            'nextLevelXp',
            'xpProgressPercent',
            'activeDates',
            'activeDaySet',
            'monthlyRate',
            'streakBroken',
            'totalXp',
            'weeklyLabels',
            'weeklyCounts',
            'monthlyXp'
        ));
    }
}
