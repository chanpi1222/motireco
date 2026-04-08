<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@example.com')->first();

        if (!$user) return;

        HabitLog::whereIn('habit_id', Habit::pluck('id'))->delete();
        Habit::query()->delete();

        // ① 習慣作成
        $habits = [
            [
                'name' => '腹筋',
                'description' => '30回やる',
                'status' => 'doing',
            ],
            [
                'name' => '読書',
                'description' => '10ページ読む',
                'status' => 'doing',
            ],
            [
                'name' => 'ランニング',
                'description' => '20分走る',
                'status' => 'todo',
            ],
            [
                'name' => 'ストレッチ',
                'description' => '寝る前に5分',
                'status' => 'doing',
            ],
            [
                'name' => '英語学習',
                'description' => '単語20個覚える',
                'status' => 'todo',
            ],
        ];

        $createdHabits = [];

        foreach ($habits as $habit) {
            $createdHabits[] = Habit::create([
                'name' => $habit['name'],
                'description' => $habit['description'],
                'status' => $habit['status'],
                'user_id' => $user->id, // ← もし無ければ削除
            ]);
        }

        // ② 過去7日分のログ作成
        foreach ($createdHabits as $habit) {
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::today()->subDays($i);

                // ランダムで達成
                if (rand(0, 1)) {
                    HabitLog::create([
                        'habit_id' => $habit->id,
                        'date' => $date,
                    ]);
                }
            }
        }
    }
}
