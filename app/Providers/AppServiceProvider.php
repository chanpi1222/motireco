<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\HabitLog;
use Carbon\Carbon;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            // 未ログイン画面では称号計算をしない
            if (!Auth::check()) {
                $view->with('title', '🧘 休憩中');
                return;
            }

            // テーブル未作成・接続未確立時も落とさない
            if (!Schema::hasTable('habit_logs')) {
                $view->with('title', '🧘 休憩中');
                return;
            }

            try {
                $today = today();

                $activeDates = HabitLog::query()
                    ->selectRaw("date(date) as d")
                    ->distinct()
                    ->pluck('d')
                    ->toArray();

                $set = array_flip($activeDates);

                $globalStreak = 0;
                $cursor = $today->toDateString();

                while (isset($set[$cursor])) {
                    $globalStreak++;
                    $cursor = Carbon::parse($cursor)->subDay()->toDateString();
                }

                $title = match (true) {
                    $globalStreak >= 60 => '🏆 レジェンド',
                    $globalStreak >= 30 => '🌟 1ヶ月達成',
                    $globalStreak >= 14 => '🔥 2週間',
                    $globalStreak >= 7  => '✅ 1週間',
                    $globalStreak >= 3  => '💪 3日',
                    $globalStreak >= 1  => '🚶 初心者',
                    default             => '🧘 休憩中',
                };

                $view->with('title', $title);
            } catch (Throwable $e) {
                // ログイン画面などを巻き込んで落とさない
                $view->with('title', '🧘 休憩中');
            }
        });
    }
}
