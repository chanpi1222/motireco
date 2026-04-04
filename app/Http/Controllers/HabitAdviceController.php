<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class HabitAdviceController extends Controller
{
    //
    /**
     * 指定した習慣に対してAIアドバイスを1件生成する
     */
    public function store(Request $request, Habit $habit)
    {
        /**
         * 指定した習慣に対してアドバイスを1件生成する
         *
         * 本来はLLM APIを利用する想定だが、
         * 開発環境では課金コストを避けるためダミー文言を返す。
         */

        // ログイン中のユーザー本人の習慣だけを対象にする
        abort_unless($habit->user_id === Auth::id(), 403);

        // 直近7日分の達成ログ日付を取得する
        $recentLogs = $habit->logs()
            ->whereDate('date', '>=', now()->subDays(7))
            ->orderBy('date')
            ->pluck('date')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
            ->values()
            ->all();

        // 習慣の状態に応じて返す候補を少し出し分ける
        if (empty($recentLogs)) {
            $adviceList = [
                "最初の1回がいちばん大事です。今日は小さく始めてみましょう。",
                "完璧を目指さず、まずは1分だけでも取り組んでみましょう。",
                "習慣化は最初の一歩からです。今日できたら十分です。",
            ];
        } else {
            $adviceList = [
                "ここまで続けられていて良い流れです。今日も無理なく1回やってみましょう。",
                "積み重ねができています。昨日より少しでも進めれば十分です。",
                "継続できているのは強みです。今日も小さく行動してつなげましょう。",
            ];
        }

        $advice = $adviceList[array_rand($adviceList)];

        return back()->with([
            'ai_advice' => $advice,
            'ai_habit_id' => $habit->id,
        ]);
    }
}
