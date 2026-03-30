<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\HabitLog;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        View::composer('*', function ($view) {

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
        });
    }
}
