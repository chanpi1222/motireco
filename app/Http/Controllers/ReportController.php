<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\HabitLog;
use App\Models\Habit;

class ReportController extends Controller
{
    public function weekly(): View
    {
        // 今日を基準日として取得
        $today = Carbon::today();

        // 直近7日間レポートにするため、6日前を開始日に設定
        // → today を含めて合計7日分になる
        $from = $today->copy()->subDays(6);

        // 集計終了時点を現在時刻で取得
        // → ランキング集計時に当日分を含めるため
        $to = now();

        // 登録されている習慣の総数を取得
        // → 週間達成率の最大値計算に使用
        $totalHabits = Habit::count();

        // レポート対象日数
        $days = 7;

        // 初期値として週間達成率を 0 にしておく
        $weeklyRate = 0;

        // 直近7日間の総達成件数を取得
        // → 週間レポートの主要指標として使う
        $weeklyDoneCount = HabitLog::query()
            ->whereBetween('date', [
                $from->startOfDay(),
                $today->endOfDay(),
            ])
            ->count();

        // 直近7日間の「日ごとの達成件数」を集計
        // → 日別レポート表示に使う
        $dailyRows = HabitLog::query()
            ->whereBetween('date', [
                $from->copy()->startOfDay(),
                $today->copy()->endOfDay(),
            ])
            ->selectRaw('date(date) as d, count(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        // View側で扱いやすいよう、日別レポート配列を作る
        $dailyReports = [];

        // 7日分すべての日付を走査し、
        // ログがない日も 0 件で埋めて表示用データを整形する
        for ($date = $from->copy(); $date->lte($today); $date->addDay()) {
            $key = $date->toDateString();

            $dailyReports[] = [
                'label' => $date->format('m/d'),
                'count' => (int) ($dailyRows[$key] ?? 0),
            ];
        }

        // 習慣が1件以上ある場合のみ週間達成率を計算
        // 最大可能達成数 = 習慣数 × 7日
        if ($totalHabits > 0) {
            $maxPossible = $totalHabits * $days;
            $weeklyRate = round(($weeklyDoneCount / $maxPossible) * 100);
        }

        // 直近7日間の習慣別ランキングを取得
        // 各Habitに weekly_done_count を付与して、達成数の多い順に並べる
        $weeklyRanking = Habit::query()
            ->withCount(['logs as weekly_done_count' => function ($query) use ($from, $to) {
                $query->whereBetween('date', [
                    $from->copy()->startOfDay(),
                    $to->copy()->endOfDay(),
                ]);
            }])
            ->orderByDesc('weekly_done_count')
            ->take(5)
            ->get();

        // 週間レポート画面へ必要な集計結果を渡す
        return view('reports.weekly', [
            'weeklyDoneCount' => $weeklyDoneCount,
            'dailyReports' => $dailyReports,
            'weeklyRate' => $weeklyRate,
            'weeklyRanking' => $weeklyRanking,
        ]);
    }
}
