<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        $habits = Habit::orderByDesc('created_at')->get();

        return view('habits.index', compact('habits'));
    }

    public function create()
    {
        return view('habits.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:todo,doing,done'],
        ]);

        Habit::create($validated);

        return redirect()
            ->route('habits.index')
            ->with('success', '習慣を追加しました!');
    }

    public function update(Request $request, Habit $habit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:todo,doing,done'],
        ]);

        $habit->update($validated);

        return redirect()
            ->route('habits.index')
            ->with('success', '習慣を更新しました!');
    }

    public function edit(Habit $habit)
    {
        return view('habits.edit', compact('habit'));
    }

    public function destroy(Habit $habit)
    {
        $habit->delete();

        return redirect()
            ->route('habits.index')
            ->with('success', '習慣を削除しました。');
    }
}

// [
//             (object)['name' => '習慣名', 'status' => '未着手'],
//             (object)['name' => '別の習慣', 'status' => '進行中'],
//         ]