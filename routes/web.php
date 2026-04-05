<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\HabitAdviceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/debug-db', function () {
    return response()->json([
        'env_db_connection' => env('DB_CONNECTION'),
        'config_default' => config('database.default'),
        'env_db_url' => env('DB_URL'),
        'env_database_url' => env('DATABASE_URL'),
        'sqlite_database' => config('database.connections.sqlite.database'),
        'pgsql_url' => config('database.connections.pgsql.url'),
    ]);
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 仕様書にある /habits/{id} を追加したいので resource を推奨（showも含む）
    Route::resource('habits', HabitController::class)->except(['show']); // 既存を壊したくない場合
    Route::get('/habits/{habit}', [HabitController::class, 'show'])->name('habits.show'); // 仕様書の「習慣詳細」

    // toggle（非同期中心）
    Route::post('/habits/{habit}/logs/toggle', [HabitLogController::class, 'toggle'])->name('habits.logs.toggle');

    // 仕様書の箱（中身は薄くてOK）
    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reports/weekly', [ReportController::class, 'weekly'])->name('reports.weekly');
    Route::get('titles', [TitleController::class, 'index'])->name('titles.index');

    Route::post('/habits/{habit}/advice', [HabitAdviceController::class, 'store'])->name('habits.advice');
});



require __DIR__ . '/auth.php';
