<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Motoreco</h1>
            <p class="text-sm text-gray-500 mt-1">習慣を記録しよう</p>
        </header>

        <!-- Add Habit Form -->
        <div class="mb-8">
            <form action="#" method="POST" class="flex gap-2">
                @csrf
                <input
                    type="text"
                    name="name"
                    placeholder="新しい習慣"
                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required>
                <button
                    type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    追加
                </button>
            </form>
        </div>

        <!-- Habits List -->
        <div class="space-y-3">
            @forelse($habits ?? [] as $habit)
            <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-gray-900 font-medium">{{ $habit->name }}</h3>
                    @if($habit->last_completed_at)
                    <p class="text-xs text-gray-500 mt-1">
                        最終記録: {{ $habit->last_completed_at->format('Y/m/d') }}
                    </p>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <!-- Complete Button -->
                    <form action="{{ route('habits.complete', $habit) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors text-sm font-medium">
                            達成
                        </button>
                    </form>

                    <!-- Delete Button -->
                    <form
                        action="{{ route('habits.destroy', $habit) }}"
                        method="POST"
                        onsubmit="return confirm('削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-3 py-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                            aria-label="Delete habit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-gray-400">習慣を追加してみましょう</p>
            </div>
            @endforelse
        </div>

        @if(session('success'))
        <div class="fixed bottom-4 right-4 bg-indigo-600 text-white px-4 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
        @endif
    </div>
</x-app-layout>