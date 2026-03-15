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
        $user = $request->user();
        $today = today();
        $now = Carbon::now();

        $xpPerDone = 10;
        $totalXp = (int) ($user->xp ?? 0);

        // 今日の完了件数
        $todayCompletedCount = HabitLog::query()
            ->whereDate('date', $today)
            ->count();

        // 今日の獲得XP
        $todayXp = $todayCompletedCount * $xpPerDone;

        // 今日の完了日数
        $monthlyCompletedDays = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw("date(date) as d")
            ->distinct()
            ->count('d');

        // 今月の達成率
        $daySoFar = now()->day;
        $monthlyRate = $daySoFar > 0 ? round(($monthlyCompletedDays / $daySoFar) * 100) : 0;

        // 今月の累計XP
        $monthlyDoneCount = HabitLog::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->count();

        $monthlyXp = $monthlyDoneCount * $xpPerDone;

        // レベル計算
        $level = $user->calcLevel($totalXp);

        $currentLevelTotalXp = $user->xpForLevel($level);
        $nextLevelTotalXp = $user->xpForLevel($level + 1);

        $currentLevelXp = max(0, $totalXp - $currentLevelTotalXp);
        $nextLevelXp = max(1, $nextLevelTotalXp - $currentLevelTotalXp);
        $xpProgressPercent = min(100, ($currentLevelXp / $nextLevelXp) * 100);

        // 全体ストリーク
        $activeDates = HabitLog::query()
            ->selectRaw("date(date) as d")
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

        // 称号
        $title = match (true) {
            $globalStreak >= 60 => '🏆 習慣レジェンド',
            $globalStreak >= 30 => '🌟 1ヶ月達成',
            $globalStreak >= 14 => '🔥 2週間マスター',
            $globalStreak >= 7 => '✅ 1週間継続',
            $globalStreak >= 3 => '💪 3日坊主卒業',
            $globalStreak >= 1 => '🚶 はじめの一歩',
            default            => '🧘 休憩中',
        };

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
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
