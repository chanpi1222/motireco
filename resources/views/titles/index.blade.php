<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <h2 class="font-semibold text-xl textt-slate-800 leading-tight">
        称号一覧
      </h2>

      <a href="{{ route('profile.edit') }}" class="text-sm text-blue-600 hover:underline">
        マイページへ戻る
      </a>
    </div>
  </x-slot>

  <div class="p-6 max-w-4xl mx-auto">
    <div class="bg-white rounded-xl p-6 shadow">
      <p class="text-slate-800 text-sm font-medium">称号一覧ページです</p>
      <p class="text-slate-500 text-sm mt-2">
        今後ここに、獲得条件付きの称号一覧を表示します。
      </p>
    </div>
  </div>

  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

      <div class="bg-white rounded-xl p-6 shadow">

        <h3 class="text-lg font-semibold text-slate-800 mb-4">
          獲得可能な称号
        </h3>

        <div class="mt-6 space-y-3">
          @foreach ($titles as $title)
          <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 {{ $globalStreak >= $title['condition'] ? 'bg-green-50 border border-green-200' : 'border border-slate-200' }}">
            <div>
              <p class="text-sm font-semibold text-slate-800">
                {{ $title['name'] }}
              </p>
              <p class="text-xs text-slate-500">
                {{ $title['condition'] }}日継続
              </p>
            </div>

            <div>
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