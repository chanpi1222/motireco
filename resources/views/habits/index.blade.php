<x-app-layout>
    {{-- レイアウト共通コンポーネント（認証済み画面の枠） --}}

    <x-slot name="header">
        {{-- ページヘッダー：一覧タイトルと新規作成導線 --}}
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Habits</h2>

            {{-- 新規習慣作成ページへの導線（ユーザー行動の起点） --}}
            <a href="{{ route('habits.create') }}" class="text-sm text-blue-600 hover:underline">
                + 新しい習慣を作成する
            </a>
        </div>
    </x-slot>

    {{-- メインコンテンツ領域（中央寄せ・カード一覧） --}}
    <div class="space-y-4 p-6 max-w-3xl mx-auto">

        {{-- 操作成功時のフィードバック表示（UX向上のため） --}}
        @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
        @endif

        {{-- 習慣データの一覧表示（存在しない場合は空状態UIを表示） --}}
        @forelse($habits as $habit)

        {{-- 各習慣カード --}}
        <div class="bg-white rounded-xl p-4 shadow-md transition-shadow duration-200 hover:shadow-lg">

            {{-- 習慣名と詳細ページ導線 --}}
            <div class="flex items-start justify-between gap-3">
                <p class="text-slate-800 text-sm font-medium">{{ $habit->name }}</p>

                {{-- 詳細画面へ遷移（編集や詳細確認用） --}}
                <a href="{{ route('habits.show', $habit) }}" class="text-xs text-blue-600 hover:underline">
                    詳細を見る
                </a>
            </div>

            {{-- ステータスに応じた見た目の切り替え（視認性向上） --}}
            @php
            // ステータスごとにバッジの色を切り替えることで、状態を直感的に理解できるようにする
            $statusClass = match ($habit->status) {
            'todo' => 'bg-slate-100 text-slate-700',
            'doing' => 'bg-blue-100 text-blue-700',
            'done' => 'bg-green-100 text-green-700',
            default => 'bg-slate-100 text-slate-700'
            };
            @endphp

            {{-- ステータス表示バッジ --}}
            <span class="mt-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusClass }}">
                {{-- 表示用ラベル（モデル側で定義されている想定） --}}
                {{ $habit->status_label }}
            </span>

            {{-- ステータス更新フォーム --}}
            <form method="POST" action="{{ route('habits.update', $habit) }}"
                class="mt-3 flex items-center gap-2">
                @csrf
                @method('PATCH')

                @php
                // バリデーションエラー時に入力値を保持するため old() を優先
                $current = old('status', $habit->status);
                @endphp

                {{-- ステータス変更セレクトボックス --}}
                <select name="status" class="block w-36 rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm text-slate-800">
                    <option value="todo" @selected($current==='todo' )>未着手</option>
                    <option value="doing" @selected($current==='doing' )>進行中</option>
                    <option value="done" @selected($current==='done' )>完了</option>
                </select>

                {{-- 更新アクション --}}
                <button type="submit" class="text-xs text-blue-600 hover:underline">
                    更新する
                </button>
            </form>

            {{-- 削除フォーム --}}
            <form method="POST" action="{{ route('habits.destroy', $habit) }}"
                onsubmit="return confirm('この習慣を削除しますか？');"
                class="mt-2">
                @csrf
                @method('DELETE')

                {{-- 誤操作防止のため確認ダイアログを挟む --}}
                <button type="submit" class="text-xs text-red-500 hover:text-red-700">
                    削除する
                </button>
            </form>
        </div>

        @empty

        {{-- 空状態UI（初回利用時のガイド） --}}
        <div class="bg-white rounded-xl p-4 shadow-md transition-shadow duration-200 hover:shadow-lg">
            <p class="text-slate-800 text-sm font-medium">まだ習慣がありません。</p>
            <p class="text-slate-400 text-xs mt-1">
                右上から「+ 新しい習慣」で追加できます。
            </p>
        </div>

        @endforelse
    </div>
</x-app-layout>