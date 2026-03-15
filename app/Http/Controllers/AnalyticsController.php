<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\HabitLog;
use App\Models\Habit;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    //
    public function index(): View
    {
        $days = 7;

        $from = Carbon::today()->subDays($days - 1);
        $to = Carbon::today();

        $weeklyRanking = Habit::query()
            ->withCount(['logs as weekly_done_count' => function ($query) use ($from, $to) {
                $query->whereBetween('date', [
                    $from->copy()->startOfDay(),
                    $to->copy()->endOfDay(),
                ]);
            }])
            ->orderByDesc('weekly_done_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        $weeklyRows = HabitLog::query()
            ->whereBetween('date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw("date(date) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $weeklyLabels = [];
        $weeklyCounts = [];

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $weeklyLabels[] = $d->format('m/d');
            $weeklyCounts[] = (int) ($weeklyRows[$key] ?? 0);
        }

        return view('analytics.index', [
            'weeklyLabels' => $weeklyLabels,
            'weeklyCounts' => $weeklyCounts,
            'weeklyRanking' => $weeklyRanking,
        ]);
    }
}
