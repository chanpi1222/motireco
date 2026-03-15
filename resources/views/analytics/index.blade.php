<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <h2 class="font-semibold text-xl text-slate-800 leading-tight">
        分析
      </h2>

      <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">
          ダッシュボードへ
        </a>

        <a href="{{ route('logs.index') }}" class="text-sm text-blue-600 hover:underline">
          ログへ
        </a>
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white rounded-xl p-6 shadow">
        <h3 class="text-lg font-semibold text-slate-800">
          週間グラフ
        </h3>

        <p class="mt-2 text-sm text-slate-600">
          直近7日間の習慣達成件数です。
        </p>

        <div class="mt-6">
          <canvas id="weeklyChart" height="120"></canvas>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow">
        <h3 class="text-lg font-semibold text-slate-800">週間ランキング</h3>
        <p class="mt-2 text-sm text-slate-600">
          直近7日間で達成回数が多かった習慣です。
        </p>

        <div class="mt-6 space-y-3">
          @forelse ($weeklyRanking as $index => $habit)
          <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
            <div class="flex items-center gap-3">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700">
                {{ $index + 1 }}
              </span>
              <div>
                <p class="text-sm font-semibold text-slate-800">{{ $habit->name }}</p>
                <p class="text-xs text-slate-500">直近7日間の達成回数</p>
              </div>
            </div>

            <p class="text-sm font-bold text-blue-600">
              {{ $habit->weekly_done_count }} 回
            </p>
          </div>
          @empty
          <div class="rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-500">
            まだランキング対象の記録がありません。
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const weeklyLabels = @json($weeklyLabels);
    const weeklyCounts = @json($weeklyCounts);

    const ctx = document.getElementById('weeklyChart');

    if (ctx) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: weeklyLabels,
          datasets: [{
            label: '完了件数',
            data: weeklyCounts,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          }
        }
      });
    }
  </script>
  @endpush
</x-app-layout>