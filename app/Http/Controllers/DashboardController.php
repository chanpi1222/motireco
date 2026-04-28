<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 今日の日付と現在日時を取得
        // → 日次判定や月次集計の基準として使う
        $today = today();
        $now = Carbon::now();
        $user = auth()->user();
        $userId = $user->id;

        // 習慣一覧を新しい順で取得
        $habits = Habit::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->withExists(['logs as is_done_today' => function ($q) use ($today) {
                $q->whereDate('date', $today);
            }])
            ->with(['logs' => function ($q) use ($today) {
                $q->select('id', 'habit_id', 'date')
                    ->whereDate('date', '>=', $today->copy()->subDays(60))
                    ->orderByDesc('date');
            }])
            ->get();

        // 各習慣ごとに「連続達成日数」と「習慣ごとの簡易称号」を付与
        // → DBに保存せず、画面表示専用のプロパティとして追加している
        $habits = $habits->map(function ($habit) use ($today) {

            // ログの日付を YYYY-MM-DD に正規化して重複を除く
            // → datetime型でも「日単位」で連続判定できるようにする
            $dates = $habit->logs
                ->map(fn($log) => Carbon::parse($log->date)->toDateString())
                ->unique()
                ->values()
                ->toArray();

            // 高速に日付存在判定できるよう配列をセット化
            $habitLogDateSet = array_flip($dates);

            $streak = 0;
            $cursor = $today->toDateString();

            // 今日からさかのぼって、連続してログが存在する間だけカウント
            // → 今日未達成なら streak は 0 になる
            while (isset($habitLogDateSet[$cursor])) {
                $streak++;
                $cursor = Carbon::parse($cursor)->subDay()->toDateString();
            }

            // Bladeでそのまま表示できるよう、動的プロパティとして追加
            $habit->streakToday = $streak;

            // 習慣単位の継続日数に応じた簡易称号を付与
            $habit->habitTitle = match (true) {
                $streak >= 30 => '🏆 マスター',
                $streak >= 14 => '🔥 継続上級者',
                $streak >= 7  => '⭐ 1週間達成',
                $streak >= 3  => '💪 3日継続',
                $streak >= 1  => '🌱 スタート',
                default       => null,
            };

            return $habit;
        });

        // 今日未達成の習慣と、今日達成済みの習慣に分ける
        // → ダッシュボード上で「今日やること」と「完了済み」を分離表示するため
        $pendingHabits = $habits->filter(fn($h) => !$h->is_done_today);
        $doneHabits = $habits->filter(fn($h) => $h->is_done_today);

        // 今日の対象習慣数 / 完了数 / 達成率を算出
        $todayTotalCount = $habits->count();
        $todayDoneCount = $doneHabits->count();
        $todayAchievementRate = $todayTotalCount > 0 ? round(($todayDoneCount / $todayTotalCount) * 100) : 0;

        // 今日の完了件数を取得
        // → HabitLog件数ベースで、XP計算や表示に利用
        $todayCompletedCount = HabitLog::query()
            ->whereDate('date', $today)
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();

        // 1件達成あたりのXP
        // → マジックナンバー化を避けるため変数化している
        $xpPerDone = config('const.xp.per_done');

        // ユーザーの累計XPを取得
        $totalXp = (int) ($user->xp ?? 0);

        // 累計XPから現在レベルを算出
        // → レベル計算ロジックはUserモデル側に寄せて責務分離している
        $level = $user->calcLevel($totalXp);

        // 現在レベル帯の開始XPと、次レベル到達に必要な累計XPを取得
        // → レベルゲージ表示に必要
        $currentLevelTotalXp = $user->xpForLevel($level);
        $nextLevelTotalXp = $user->xpForLevel($level + 1);

        // 現レベル帯の中でどれだけXPを貯めたか
        $currentLevelXp = max(0, $totalXp - $currentLevelTotalXp);

        // 次レベルまでに必要なXP量
        // → 0除算防止のため最低1を保証
        $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);

        // ゲージ用の進捗率（最大100%）
        $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

        // 今日獲得したXP
        $todayXp = $todayCompletedCount * $xpPerDone;

        // 今月の完了件数を取得し、月間XPを算出
        // → 月次の頑張りを見える化するため
        $monthlyDoneCount = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();

        $monthlyXp = $monthlyDoneCount * $xpPerDone;

        // 今月の「達成した日数」を取得
        // → 1日に複数件ログがあっても、その日は1日として数える
        $monthlyCompletedDays = HabitLog::query()
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw('date(date) as d')
            ->distinct()
            ->get()
            ->count();

        // 今月何日経過しているか
        $daySoFar = now()->day;

        // 今月の達成率（日ベース）
        $monthlyRate = $daySoFar > 0 ? round(($monthlyCompletedDays / $daySoFar) * 100) : 0;

        // 今月ログがあった日を YYYY-MM-DD の配列で取得
        // → カレンダーやヒートマップ表示用
        $activeDays = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->selectRaw("date(date) as d")
            ->distinct()
            ->pluck('d')
            ->toArray();

        // Bladeで高速に「その日がアクティブか」を判定できるようセット化
        $activeDaySet = array_flip($activeDays);

        // 直近60日分の「ログがあった日」を取得
        // → 全体ストリーク計算用
        $activeDates = HabitLog::query()
            ->whereDate('date', '>=', $today->copy()->subDays(60))
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->selectRaw("date(date) as d")
            ->distinct()
            ->pluck('d')
            ->toArray();

        $globalStreak = 0;
        $activeDateSet = array_flip($activeDates);

        $cursor = $today->toDateString();

        // 今日からさかのぼって、アプリ全体として何日連続で活動しているかを算出
        while (isset($activeDateSet[$cursor])) {
            $globalStreak++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        // 全体ストリークに応じた称号を決定
        $globalTitle = match (true) {
            $globalStreak >= 60 => '🏆 習慣レジェンド',
            $globalStreak >= 30 => '🌟 1ヶ月達成',
            $globalStreak >= 14 => '🔥 2週間マスター',
            $globalStreak >= 7 => '✅ 1週間継続',
            $globalStreak >= 3 => '💪 3日坊主卒業',
            $globalStreak >= 1 => '🚶 はじめの一歩',
            default            => '🧘 休憩中',
        };

        // ストリークが途切れたかどうかを判定
        // 昨日にログがなく、かつ現在ストリークが0なら「途切れた」とみなす
        $yesterday = Carbon::yesterday()->toDateString();

        $didYesterday = HabitLog::query()
            ->whereDate('date', $yesterday)
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->exists();

        $streakBroken = !$didYesterday && $globalStreak === 0;

        // 直近7日間の週間グラフを作成
        // → 日別の達成件数推移を表示するため
        $weeklyRangeDays = 7;

        $chartStartDate = Carbon::today()->subDays($weeklyRangeDays - 1);
        $chartEndDate   = Carbon::today();

        $weeklyRows = HabitLog::query()
            ->whereBetween('date', [$chartStartDate->startOfDay(), $chartEndDate->endOfDay()])
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->selectRaw("date(date) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        // グラフ表示用に、存在しない日も0件で埋めて7日分そろえる
        $weeklyLabels = [];
        $weeklyCounts = [];

        for ($date = $chartStartDate->copy(); $date->lte($chartEndDate); $date->addDay()) {
            $key = $date->toDateString();
            $weeklyLabels[] = $date->format('m/d');
            $weeklyCounts[] = (int) ($weeklyRows[$key] ?? 0);
        }

        // ダッシュボード表示に必要な値をまとめて渡す
        return view('dashboard', compact(
            'pendingHabits',
            'doneHabits',
            'todayCompletedCount',
            'monthlyCompletedDays',
            'globalStreak',
            'globalTitle',
            'todayXp',
            'xpPerDone',
            'level',
            'currentLevelXp',
            'nextLevelXp',
            'xpProgressPercent',
            'activeDaySet',
            'monthlyRate',
            'streakBroken',
            'totalXp',
            'weeklyLabels',
            'weeklyCounts',
            'monthlyXp',
            'todayTotalCount',
            'todayDoneCount',
            'todayAchievementRate'
        ));
    }
}
