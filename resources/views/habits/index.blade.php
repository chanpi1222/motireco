<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Habits</h2>
            <a href="{{ route('habits.create') }}" class="text-sm text-blue-600 hover:underline">
                + 新しい習慣を作成する
            </a>
        </div>
    </x-slot>

    <div class="space-y-4 p-6 max-w-3xl mx-auto">

        @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
        @endif

        @forelse($habits as $habit)
        <a href="#" class="block">
            <div class="bg-white rounded-xl p-4 shadow-md transition-shadow duration-200 hover:shadow-lg">
                <p class="text-slate-800 text-sm font-medium">{{ $habit->name }}</p>

                @php
                $statusClass = match ($habit->status) {
                'todo' => 'bg-slate-100 text-slate-700',
                'doing' => 'bg-blue-100 text-blue-700',
                'done' => 'bg-green-100 text-green-700',
                default => 'bg-slate-100 text-slate-700'
                };
                @endphp

                <span class="mt-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusClass }}">
                    {{ $habit->status_label }}
                </span>

                <form method="POST" action="{{ route('habits.update', $habit) }}"
                    class="mt-3 flex items-center gap-2">
                    @csrf
                    @method('PATCH')

                    @php
                    $current = old('status', $habit->status);
                    @endphp
                    <select name="status" class="block w-36 rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm text-slate-800">
                        <option value="todo" @selected($current==='todo' )>未着手</option>
                        <option value="doing" @selected($current==='doing' )>進行中</option>
                        <option value="done" @selected($current==='done' )>完了</option>
                    </select>

                    <button type="submit" class="text-xs text-blue-600 hover:underline">
                        更新する
                    </button>
                </form>

                <form method="POST" action="{{ route('habits.destroy', $habit) }}" onsubmit="return confirm('この習慣を削除しますか？');" class="mt-2">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">
                        削除する
                    </button>
                </form>
            </div>
        </a>
        @empty
        <div class="bg-white rounded-xl p-4 shadow-md transition-shadow duration-200 hover:shadow-lg">
            <p class="text-slate-800 text-sm font-medium">まだ習慣がありません。</p>
            <p class="text-slate-400 text-xs mt-1">右上から「+ 新しい習慣」で追加できます。</p>
        </div>
        @endforelse
    </div>
</x-app-layout>