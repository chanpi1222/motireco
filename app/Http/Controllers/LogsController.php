<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\HabitLog;

class LogsController extends Controller
{
    //
    public function index(): View
    {
        $month = request('month');

        $base = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : Carbon::now()->startOfMonth();

        $start = $base->copy()->startOfMonth();
        $end = $base->copy()->endOfMonth();

        $rows = HabitLog::query()
            ->whereBetween('date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->selectRaw("date(date) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $heatmapDays = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();
            $count = (int) ($rows[$key] ?? 0);

            $level = match (true) {
                $count >= 4 => 4,
                $count >= 3 => 3,
                $count >= 2 => 2,
                $count >= 1 => 1,
                default => 0,
            };

            $heatmapDays[] = [
                'date' => $key,
                'label' => $date->format('m/d'),
                'count' => $count,
                'level' => $level,
            ];
        }

        return view('logs.index', [
            'heatmapDays' => $heatmapDays,
            'currentMonth' => $base->format('m/d'),
            'prevMonth' => $base->copy()->subMnoth()->format('Y-m'),
            'nextMonth' => $base->copy()->addMonth()->format('Y-m'),
        ]);
    }
}
