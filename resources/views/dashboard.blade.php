<x-app-layout>
    {{-- ページ上部ヘッダーエリア（タイトル + ナビゲーション） --}}
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            {{-- ページタイトル表示 --}}
            <h2 class="font-semibold text-xl textt-slate-800 leading-tight">
                称号一覧
            </h2>

            {{-- マイページへの導線（UX向上のため戻りリンクを配置） --}}
            <a href="{{ route('profile.edit') }}" class="text-sm text-blue-600 hover:underline">
                マイページへ戻る
            </a>
        </div>
    </x-slot>

    {{-- ページ説明セクション（今後の拡張を想定したプレースホルダー） --}}
    <div class="p-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-xl p-6 shadow">
            <p class="text-slate-800 text-sm font-medium">称号一覧ページです</p>
            <p class="text-slate-500 text-sm mt-2">
                今後ここに、獲得条件付きの称号一覧を表示します。
            </p>
        </div>
    </div>

    {{-- 称号一覧表示エリア --}}
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl p-6 shadow">

                {{-- セクション見出し --}}
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    獲得可能な称号
                </h3>

                {{-- Controllerから渡された称号リストをループ表示 --}}
                <div class="mt-6 space-y-3">
                    @foreach ($titles as $title)

                    {{--
            各称号カード
            ・globalStreak（現在の連続達成日数）と条件を比較
            ・達成済みの場合は背景色を変更して視覚的に区別
          --}}
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 
            {{ $globalStreak >= $title['condition'] 
              ? 'bg-green-50 border border-green-200' 
              : 'border border-slate-200' }}">

                        <div>
                            {{-- 称号名 --}}
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $title['name'] }}
                            </p>

                            {{-- 達成条件（日数） --}}
                            <p class="text-xs text-slate-500">
                                {{ $title['condition'] }}日継続
                            </p>
                        </div>

                        <div>
                            {{--
                達成状態の表示切り替え
                ・条件達成 → 達成（強調表示）
                ・未達成 → 未達成（グレー表示）
              --}}
                            @if ($globalStreak >= $title['condition'])
                            <span class="text-green-600 font-bold">達成</span>
                            @else
                            <span class="text-slate-400">未達成</span>
                            @endif
                        </div>
                    </div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>