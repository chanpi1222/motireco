<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\HabitLog;
use App\Models\Habit;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        // 集計対象の日数
        // → 直近7日間の分析を行う
        $days = 7;

        // 集計期間の開始日と終了日を定義
        // today を基準に、過去7日分を含める
        $from = Carbon::today()->subDays($days - 1);
        $to = Carbon::today();

        // 習慣ごとの週間達成回数を集計し、ランキング用データを取得
        // withCount を使うことで、各 Habit に weekly_done_count を付与している
        $weeklyRanking = Habit::query()
            ->withCount(['logs as weekly_done_count' => function ($query) use ($from, $to) {
                // 対象期間内のログだけをカウント
                $query->whereBetween('date', [
                    $from->copy()->startOfDay(),
                    $to->copy()->endOfDay(),
                ]);
            }])
            // 週間達成回数の多い順に並べる
            ->orderByDesc('weekly_done_count')
            // 同率の場合は名前順で安定した並びにする
            ->orderBy('name')
            // 上位5件のみ表示
            ->take(5)
            ->get();

        // 直近7日間の「日ごとの総達成件数」を集計
        // → 週間グラフ表示に使う
        $weeklyRows = HabitLog::query()
            ->whereBetween('date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw("date(date) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        // グラフ描画用のラベル配列と件数配列を用意
        $weeklyLabels = [];
        $weeklyCounts = [];

        // 対象期間の全日付を順番に走査し、
        // ログが存在しない日も 0 件で埋める
        // → グラフの横軸を欠けなく表示するため
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $weeklyLabels[] = $d->format('m/d');
            $weeklyCounts[] = (int) ($weeklyRows[$key] ?? 0);
        }

        // 分析画面へ必要なデータを渡す
        return view('analytics.index', [
            'weeklyLabels' => $weeklyLabels,
            'weeklyCounts' => $weeklyCounts,
            'weeklyRanking' => $weeklyRanking,
        ]);
    }
}
