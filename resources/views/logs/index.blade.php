<x-app-layout>
  {{-- ページヘッダー：画面タイトルとナビゲーションリンク --}}
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <h2 class="font-semibold text-xl text-slate-800 leading-tight">Logs</h2>

      {{-- 他画面への導線（ダッシュボード / マイページ） --}}
      <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">
          ダッシュボードへ
        </a>
        <a href="{{ route('profile.edit') }}" class="text-sm text-blue-600 hover:underline">
          マイページへ
        </a>
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white rounded-xl p-6 shadow sm:rounded-lg">

        {{-- ヒートマップセクション：月ごとの習慣達成状況を可視化 --}}
        <div class="flex items-center justify-between gap-4">
          <div>
            <h3 class="text-lg font-semibold text-slate-800">継続ヒートマップ</h3>

            {{-- 現在表示している月の説明 --}}
            <p class="mt-2 text-sm text-slate-600">
              {{ $currentMonth }} の達成状況です。色が濃いほど、その日の達成件数が多いです。
            </p>
          </div>

          {{-- 月の切り替え（前月 / 次月） --}}
          <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('logs.index', ['month' => $prevMonth]) }}" class="text-blue-600 hover:underline">
              ← 前月
            </a>

            <a href="{{ route('logs.index', ['month' => $nextMonth]) }}" class="text-blue-600 hover:underline">
              次月 →
            </a>
          </div>
        </div>

        {{-- ヒートマップ本体 --}}
        <div class="mt-6">
          <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-10 gap-2">

            {{-- 各日ごとの達成状況をループ表示 --}}
            @foreach ($heatmapDays as $day)

            @php
            // 達成件数に応じて色の濃さを変更（視覚的に達成度を把握しやすくするため）
            $bgClass = match ($day['level']) {
            4 => 'bg-green-600 text-white',
            3 => 'bg-green-500 text-white',
            2 => 'bg-green-400 text-white',
            1 => 'bg-green-200 text-slate-700',
            default => 'bg-slate-100 text-slate-400',
            };
            @endphp

            {{-- 1日分のセル（件数と日付を表示） --}}
            <div
              class="h-14 rounded-lg flex flex-col items-center justify-center text-xs shadow-sm {{ $bgClass }}"
              title="{{ $day['date'] }} / {{ $day['count'] }}件">

              {{-- 日付ラベル（例：1, 2, 3...） --}}
              <span>{{ $day['label'] }}</span>

              {{-- 達成件数 --}}
              <span class="mt-1 font-semibold">{{ $day['count'] }}</span>
            </div>
            @endforeach
          </div>
        </div>

        {{-- ヒートマップの凡例（色の意味をユーザーに伝える） --}}
        <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
          <span>少</span>
          <span class="w-4 h-4 rounded bg-slate-100 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-200 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-400 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-500 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-600 inline-block"></span>
          <span>多</span>
        </div>

        {{-- 最近の記録一覧（直近の達成ログを表示） --}}
        <div class="mt-8">
          <h3 class="text-base font-bold text-slate-800 mb-3">最近の記録</h3>

          {{-- ログが存在する場合 --}}
          @forelse ($recentLogs as $log)
          <div class="bg-slate-50 rounded-lg p-3 mb-2 text-sm flex items-center justify-between">

            {{-- 習慣名と記録日 --}}
            <div>
              <p class="text-slate-800 font-medium">{{ $log->habit->name }}</p>
              <p class="text-slate-500 text-xs mt-1">
                {{ $log->created_at->format('Y-m-d') }}
              </p>
            </div>

            {{-- 完了ステータス表示 --}}
            <span class="text-green-600 text-xs font-semibold">
              ✓ 完了
            </span>
          </div>

          {{-- ログがない場合 --}}
          @empty
          <p class="text-sm text-slate-500">まだ記録がありません</p>
          @endforelse
        </div>

      </div>
    </div>
  </div>
</x-app-layout>