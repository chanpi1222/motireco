<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        // 作成日時の新しい順で習慣一覧を取得
        // → 最新の習慣を上に表示することでUXを向上
        $habits = Habit::where('user_id', auth->id())
            ->orderByDesc('created_at')
            ->get();

        // 一覧画面へデータを渡す
        return view('habits.index', compact('habits'));
    }

    public function create()
    {
        // 習慣作成フォームを表示するだけのシンプルな責務
        return view('habits.create');
    }

    public function show(\App\Models\Habit $habit)
    {
        // ルートモデルバインディングにより対象のHabitが自動取得される
        // → ID取得やfind処理をControllerで書かなくて済む
        abort_unless($habit->user_id === auth()->id(), 403);

        return view('habits.show', compact('habit'));
    }

    public function store(Request $request)
    {
        // 入力値のバリデーション
        // → 不正なデータの保存を防ぐ（必須・型・最大文字数・許可値）
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:todo,doing,done'],
        ]);

        Habit::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        // バリデーション済みデータのみを保存
        // → マスアサインメント対策（fillable前提）
        // Habit::create($validated);

        // 一覧画面へリダイレクトし、成功メッセージをフラッシュ
        return redirect()
            ->route('habits.index')
            ->with('success', '習慣を追加しました!');
    }

    public function update(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === auth()->id(), 403);
        // 部分更新を許可するため 'sometimes' を使用
        // → フィールドが送られてきた場合のみバリデーションする
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'required', 'in:todo,doing,done'],
        ]);

        // 指定された習慣を更新
        $habit->update($validated);

        // 更新後は一覧へ戻し、ユーザーにフィードバック
        return redirect()
            ->route('habits.index')
            ->with('success', '習慣を更新しました!');
    }

    public function edit(Habit $habit)
    {
        // 編集対象の習慣をフォームに渡す
        // → 既存データを初期値として表示するため
        abort_unless($habit->user_id === auth()->id(), 403);

        return view('habits.edit', compact('habit'));
    }

    public function destroy(Habit $habit)
    {
        abort_unless($habit->user_id === auth()->id(), 403);
        // 対象の習慣を削除
        // → 関連するログの扱いはDB設計（外部キー制約など）に依存
        $habit->delete();

        // 削除後は一覧へ戻し、成功メッセージを表示
        return redirect()
            ->route('habits.index')
            ->with('success', '習慣を削除しました。');
    }
}
