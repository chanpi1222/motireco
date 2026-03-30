<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\HabitLog;

class LogsController extends Controller
{
    public function index(Request $request): View
    {

        // クエリパラメータをバリデーション
        // → 不正なフォーマット（例: 2026-13 など）を防ぐ

        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        // クエリパラメータから対象月を取得
        // 例: ?month=2026-03
        $month = $validated['month'] ?? now()->format('Y-m');

        // month が指定されていればその月の月初を基準にし、
        // 未指定なら現在月を基準にする
        // → ログ画面で月送りできるようにするため
        $base = $month
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : Carbon::now()->startOfMonth();

        // 対象月の開始日と終了日を取得
        // → 月単位のヒートマップ集計範囲として使う
        $start = $base->copy()->startOfMonth();
        $end = $base->copy()->endOfMonth();

        // 対象月のログ件数を日別に集計
        // date(date) で datetime から日付部分だけを取り出し、
        // 1日ごとの件数を count(*) で取得している
        $rows = HabitLog::query()
            ->whereBetween('date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->selectRaw("date(date) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        // ヒートマップ表示用の配列を作成
        $heatmapDays = [];

        // 月初から月末まで1日ずつ走査し、
        // ログがない日も 0 件として埋める
        // → 表示側で日付抜けのないカレンダー表現にするため
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();
            $count = (int) ($rows[$key] ?? 0);

            // 件数に応じてヒートマップの濃さレベルを決定
            // → View側では level を見てCSSクラスや色分けに使える
            $level = match (true) {
                $count >= 4 => 4,
                $count >= 3 => 3,
                $count >= 2 => 2,
                $count >= 1 => 1,
                default => 0,
            };

            // 画面表示に必要な情報を1日分ずつ格納
            $heatmapDays[] = [
                'date' => $key,                 // YYYY-MM-DD（内部判定用）
                'label' => $date->format('m/d'), // 表示用
                'count' => $count,             // その日の達成件数
                'level' => $level,             // ヒートマップ濃度
            ];
        }

        // 直近のログを10件取得
        // Habitも一緒に読み込んで、View側で習慣名を表示しやすくする
        // → with() により N+1 問題を防ぐ
        $recentLogs = \App\Models\HabitLog::with('habit')
            ->latest()
            ->limit(10)
            ->get();

        // ログ一覧画面へ必要なデータを渡す
        return view('logs.index', [
            'heatmapDays' => $heatmapDays,
            'currentMonth' => $base->format('m/d'),
            'prevMonth' => $base->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $base->copy()->addMonth()->format('Y-m'),
            'recentLogs' => $recentLogs
        ]);
    }
}
