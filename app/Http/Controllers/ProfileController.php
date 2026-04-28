<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\HabitLog;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // ログイン中ユーザーを取得
        $user = $request->user();
        $userId = $user->id;

        // 日次・月次集計の基準となる日付を取得
        $today = today();
        $now = Carbon::now();

        // 1件達成あたりのXP
        $xpPerDone = config('const.xp.per_done');

        // 累計XPを取得
        $totalXp = (int) ($user->xp ?? 0);

        // 今日の完了件数を取得
        // → プロフィール画面でも当日の活動量を表示するため
        $todayCompletedCount = HabitLog::query()
            ->whereDate('date', $today)
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();

        // 今日の獲得XPを算出
        $todayXp = $todayCompletedCount * $xpPerDone;

        // 今月の「達成した日数」を取得
        // → 1日に複数件記録しても、その日は1日として数える
        $monthlyCompletedDays = HabitLog::query()
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw("date(date) as d")
            ->distinct()
            ->get()
            ->count();

        // 今月の経過日数を取得し、達成率を算出
        $daySoFar = now()->day;
        $monthlyRate = $daySoFar > 0 ? round(($monthlyCompletedDays / $daySoFar) * 100) : 0;

        // 今月の総達成件数を取得し、月間XPを算出
        $monthlyDoneCount = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();

        $monthlyXp = $monthlyDoneCount * $xpPerDone;

        // 累計XPから現在レベルを算出
        // → レベル計算ロジックはUserモデル側に集約している前提
        $level = $user->calcLevel($totalXp);

        // 現在レベル帯の開始XPと次レベル到達に必要な累計XPを取得
        $currentLevelTotalXp = $user->xpForLevel($level);
        $nextLevelTotalXp = $user->xpForLevel($level + 1);

        // 現レベル帯でどれだけXPを貯めたかを算出
        $currentLevelXp = max(0, $totalXp - $currentLevelTotalXp);

        // 次レベルまでに必要なXP量を算出
        // → 0除算防止のため最低1を保証
        $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);

        // レベルゲージ表示用の進捗率
        $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

        // 全体ストリーク計算用に、直近60日分の活動日を取得
        // → 「その日に1件でもログがあれば活動日」とみなす
        $activeDates = HabitLog::query()
            ->selectRaw("date(date) as d")
            ->whereDate('date', '>=', $today->copy()->subDays(60))
            ->whereHas('habit', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->distinct()
            ->pluck('d')
            ->toArray();

        $globalStreak = 0;
        $set = array_flip($activeDates);

        $cursor = $today->toDateString();

        // 今日から過去にさかのぼって、連続して活動している日数を数える
        while (isset($set[$cursor])) {
            $globalStreak++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        // 全体ストリークに応じてプロフィール用の称号を決定
        $title = match (true) {
            $globalStreak >= 60 => '🏆 習慣レジェンド',
            $globalStreak >= 30 => '🌟 1ヶ月達成',
            $globalStreak >= 14 => '🔥 2週間マスター',
            $globalStreak >= 7 => '✅ 1週間継続',
            $globalStreak >= 3 => '💪 3日坊主卒業',
            $globalStreak >= 1 => '🚶 はじめの一歩',
            default            => '🧘 休憩中',
        };

        // プロフィール編集画面へ、統計情報とユーザー情報を渡す
        return view('profile.edit', [
            'user' => $user,
            'todayCompletedCount' => $todayCompletedCount,
            'todayXp' => $todayXp,
            'monthlyXp' => $monthlyXp,
            'xpPerDone' =>  $xpPerDone,
            'totalXp' => $totalXp,
            'level' => $level,
            'currentLevelXp' => $currentLevelXp,
            'nextLevelXp' => $nextLevelXp,
            'xpProgressPercent' => $xpProgressPercent,
            'globalStreak' => $globalStreak,
            'title' => $title,
            'monthlyCompletedDays' => $monthlyCompletedDays,
            'monthlyRate' => $monthlyRate,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // バリデーション済みデータをユーザーモデルへ反映
        // → fill により一括代入し、その後 save で保存する
        $request->user()->fill($request->validated());

        // メールアドレスが変更された場合は、再認証が必要になるため
        // メール認証日時をリセットする
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // 変更内容を保存
        $request->user()->save();

        // 編集画面へ戻し、更新完了ステータスをフラッシュ
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // アカウント削除前に現在のパスワード確認を必須にする
        // → 誤操作や第三者操作の防止
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // 削除対象ユーザーを取得
        $user = $request->user();

        // 先にログアウトし、認証状態を解除する
        Auth::logout();

        // ユーザーアカウントを削除
        $user->delete();

        // セッションを無効化し、CSRFトークンも再生成する
        // → 削除後の不正利用や古いセッションの残存を防ぐ
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // トップページへリダイレクト
        return Redirect::to('/');
    }
}
