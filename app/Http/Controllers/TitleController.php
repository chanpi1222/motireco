<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\View\View;
use App\Models\HabitLog;

class TitleController extends Controller
{
    public function index(): View
    {
        // 今日の日付を取得
        // → ストリーク計算の起点として使う
        $today = today();

        // 活動があった日をすべて取得
        // date(date) で日時ではなく「日単位」に正規化し、
        // 1日に複数ログがあっても1日として扱う
        $activeDates = HabitLog::query()
            ->whereHas('habit', fn($query) => $query->where('user_id', auth()->id()))
            ->selectRaw("date(date) as d")
            ->distinct()
            ->pluck('d')
            ->toArray();

        // 日付の存在判定を高速にするためセット化
        $set = array_flip($activeDates);

        $globalStreak = 0;
        $cursor = $today->toDateString();

        // 今日から過去に向かって、連続して活動している日数を数える
        // → 今日未達成ならストリークは0になる
        while (isset($set[$cursor])) {
            $globalStreak++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        // 称号一覧を定義
        // condition はその称号を獲得するために必要な連続日数
        // → View側では globalStreak と比較して達成済み判定に使える
        $titles = [
            ['name' => '🚶 はじめの一歩', 'condition' => 1],
            ['name' => '💪 3日坊主卒業', 'condition' => 3],
            ['name' => '✅ 1週間継続', 'condition' => 7],
            ['name' => '🔥 2週間マスター', 'condition' => 14],
            ['name' => '🌟 1ヶ月達成', 'condition' => 30],
            ['name' => '🏆 習慣レジェンド', 'condition' => 60],
        ];

        // 称号一覧画面へ、称号定義と現在の全体ストリークを渡す
        return view('titles.index', [
            'titles' => $titles,
            'globalStreak' => $globalStreak,
        ]);
    }
}
