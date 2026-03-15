<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <h2 class="font-semibold text-xl text-slate-800 leading-tight">Logs</h2>

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
        <div class="flex items-center justify-between gap-4">
          <div>
            <h3 class="text-lg font-semibold text-slate-800">継続ヒートマップ</h3>
            <p class="mt-2 text-sm text-slate-600">
              {{ $currentMonth }} の達成状況です。色が濃いほど、その日の達成件数が多いです。
            </p>
          </div>

          <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('logs.index', ['month' => $prevMonth]) }}" class="text-blue-600 hover:underline">
              ← 前月
            </a>

            <a href="{{ route('logs.index', ['month' => $nextMonth]) }}" class="text-blue-600 hover:underline">
              次月 →
            </a>
          </div>
        </div>

        <div class="mt-6">
          <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-10 gap-2">
            @foreach ($heatmapDays as $day)
            @php
            $bgClass = match ($day['level']) {
            4 => 'bg-green-600 text-white',
            3 => 'bg-green-500 text-white',
            2 => 'bg-green-400 text-white',
            1 => 'bg-green-200 text-slate-700',
            default => 'bg-slate-100 text-slate-400',
            };
            @endphp

            <div
              class="h-14 rounded-lg flex flex-col items-center justify-center text-xs shadow-sm {{ $bgClass }}"
              title="{{ $day['date'] }} / {{ $day['count'] }}件">
              <span>{{ $day['label'] }}</span>
              <span class="mt-1 font-semibold">{{ $day['count'] }}</span>
            </div>
            @endforeach
          </div>
        </div>

        <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
          <span>少</span>
          <span class="w-4 h-4 rounded bg-slate-100 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-200 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-400 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-500 inline-block"></span>
          <span class="w-4 h-4 rounded bg-green-600 inline-block"></span>
          <span>多</span>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>