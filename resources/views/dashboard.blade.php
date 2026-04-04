<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Dashboard
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('habits.index') }}" class="text-sm text-blue-600 hover:underline">
                    習慣一覧へ
                </a>

                <a href="{{ route('habits.create') }}"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    習慣を追加
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 p-6 max-w-4xl mx-auto">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-medium tracking-wide text-slate-500">今日の完了数</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">
                    <span id="todayCompletedCount">{{ $todayCompletedCount }}</span>
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-medium tracking-wide text-slate-500">累計XP</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">
                    <span id="totalXp">{{ $totalXp }}</span>
                </p>
                <p class="mt-1 text-xs text-slate-400">習慣達成でXPが増加します</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-xs font-medium tracking-wide text-slate-500">現在レベル</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        Lv.<span id="currentLevel">{{ $level }}</span>
                    </p>
                </div>

                <div class="mt-4 h-2.5 rounded-full bg-slate-200">
                    <div id="xpProgressBar" class="h-2.5 rounded-full bg-blue-500 transition-all duration-300" style="width: {{ $xpProgressPercent }}%;"></div>
                </div>

                <p class="mt-2 text-xs text-slate-500">
                    <span id="currentLevelXp">{{ $currentLevelXp }}</span> /
                    <span id="nextLevelXp">{{ $nextLevelXp }}</span> XP
                </p>
            </div>
        </div>

        {{-- 未達 / 完了 --}}
        <div class="grid gap-6 md:grid-cols-2">

            {{-- 未達 --}}
            <div id="pendingList">
                <h3 class="text-base font-bold text-slate-800 mb-3">
                    🔥 今日やること
                </h3>

                @forelse ($pendingHabits as $habit)
                <div class="min-h-[140px] bg-white rounded-xl p-4 shadow-sm mb-3 border border-slate-200 border-l-4 border-l-blue-500 flex flex-col"
                    data-habit-card
                    data-habit-id="{{ $habit->id }}"
                    data-toggle-url="{{ route('habits.logs.toggle', ['habit' => $habit->id]) }}"
                    data-state="pending">

                    <div>
                        <p class="text-slate-900 text-base font-semibold">
                            {{ $habit->name }}
                        </p>

                        <div class="h-5 mt-1">
                            @if ($habit->description)
                            <p class="text-xs text-slate-500 truncate">
                                {{ $habit->description }}
                            </p>
                            @endif
                        </div>

                        <p class="js-status text-blue-600 text-xs mt-2">● 未完了</p>
                        <p class="js-streak text-slate-500 text-xs mt-1">
                            連続: {{ $habit->streak_today }} 日
                        </p>
                    </div>

                    <div class="mt-auto pt-3">
                        <button type="button"
                            class="js-toggle inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                            今日は完了
                        </button>
                    </div>
                </div>
                @empty
                <div id="pendingEmpty" class="bg-white rounded-xl p-4 shadow">
                    <p class="text-slate-600 text-sm">今日の未達はありません 🎉</p>
                </div>
                @endforelse
            </div>

            {{-- 完了済み --}}
            <div id="doneList">
                <h3 class="text-base font-bold text-slate-800 mb-3">
                    ✓ 完了済み
                </h3>

                @forelse ($doneHabits as $habit)
                <div class="min-h-[140px] bg-white rounded-xl p-4 shadow-sm mb-3 border border-slate-200 border-l-4 border-l-green-400 flex flex-col"
                    data-habit-card
                    data-habit-id="{{ $habit->id }}"
                    data-toggle-url="{{ route('habits.logs.toggle', ['habit' => $habit->id]) }}"
                    data-state="done">

                    <div>
                        <p class="text-slate-900 text-base font-semibold">
                            {{ $habit->name }}
                        </p>

                        <div class="h-5 mt-1">
                            @if ($habit->description)
                            <p class="text-xs text-slate-500 truncate">
                                {{ $habit->description }}
                            </p>
                            @endif
                        </div>

                        <p class="js-status text-green-600 text-xs mt-2 font-semibold">
                            ✓ 完了
                        </p>
                        <p class="js-streak text-slate-500 text-xs mt-1">
                            連続: {{ $habit->streak_today }} 日
                        </p>
                    </div>

                    <div class="mt-auto pt-3">
                        <button type="button"
                            class="js-toggle inline-flex items-center rounded-lg bg-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-300">
                            取り消す
                        </button>
                    </div>
                </div>

                @empty
                <div id="doneEmpty" class="bg-white rounded-xl p-4 shadow">
                    <p class="text-slate-600 text-sm">完了した習慣はありません。</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- XP変動トースト --}}
    <div id="xpToast" class="pointer-events-none fixed right-4 top-4 z-[9999] hidden rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-lg"></div>

    {{-- レベルアップ表示 --}}
    <div id="levelUpToast"
        class="pointer-events-none fixed inset-0 z-[9998] hidden items-center justify-center">
        <div class="rounded-2xl bg-white/95 px-8 py-6 text-center shadow-2xl ring-1 ring-slate-200 backdrop-blur">
            <p class="text-sm font-semibold tracking-[0.2em] text-amber-500">LEVEL UP</p>
            <p id="levelUpText" class="mt-2 text-2xl font-bold text-slate-900"></p>
        </div>
    </div>

    @push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        async function postToggle(url) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                }
            });

            return await res.json();
        }

        function updateEmptyStates() {
            const pendingList = document.getElementById('pendingList');
            const doneList = document.getElementById('doneList');
            const pendingEmpty = document.getElementById('pendingEmpty');
            const doneEmpty = document.getElementById('doneEmpty');

            const pendingCards = pendingList?.querySelectorAll('[data-habit-card]').length ?? 0;
            const doneCards = doneList?.querySelectorAll('[data-habit-card]').length ?? 0;

            if (pendingEmpty) {
                pendingEmpty.classList.toggle('hidden', pendingCards > 0);
            }

            if (doneEmpty) {
                doneEmpty.classList.toggle('hidden', doneCards > 0);
            }
        }

        function moveCard(card, done) {
            const pendingList = document.getElementById('pendingList');
            const doneList = document.getElementById('doneList');
            const button = card.querySelector('.js-toggle');
            const status = card.querySelector('.js-status');

            if (done) {
                card.dataset.state = 'done';
                doneList.appendChild(card);

                card.classList.remove('border-l-blue-500');
                card.classList.add('border-l-green-400');

                if (status) {
                    status.textContent = '✓ 完了';
                    status.className = 'js-status text-green-600 text-xs mt-2 font-semibold';
                }

                button.textContent = '取り消す';
                button.className = 'js-toggle inline-flex items-center rounded-lg bg-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-300';
            } else {
                card.dataset.state = 'pending';
                pendingList.appendChild(card);

                card.classList.remove('border-l-green-400');
                card.classList.add('border-l-blue-500');

                if (status) {
                    status.textContent = '● 未完了';
                    status.className = 'js-status text-blue-600 text-xs mt-2';
                }

                button.textContent = ' 今日は完了';
                button.className = 'js-toggle inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700';
            }

            card.classList.add('card-enter');
            setTimeout(() => card.classList.remove('card-enter'), 280);

            updateEmptyStates();
        }

        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.js-toggle');
            if (!btn) return;

            const card = btn.closest('[data-habit-card]');
            const url = card.dataset.toggleUrl;

            try {
                const data = await postToggle(url);
                moveCard(card, data.done);

                const todayCompletedCountEl = document.getElementById('todayCompletedCount');
                const totalXpEl = document.getElementById('totalXp');
                const currentLevelEl = document.getElementById('currentLevel');
                const currentLevelXpEl = document.getElementById('currentLevelXp');
                const nextLevelXpEl = document.getElementById('nextLevelXp');
                const xpProgressBarEl = document.getElementById('xpProgressBar');
                const streakEl = card.querySelector('.js-streak');

                if (streakEl && typeof data.streak_today === 'number') {
                    streakEl.textContent = `連続: ${data.streak_today} 日`;
                }

                if (todayCompletedCountEl && typeof data.today_completed_count === 'number') {
                    todayCompletedCountEl.textContent = data.today_completed_count;
                }

                if (totalXpEl && typeof data.total_xp === 'number') {
                    totalXpEl.textContent = data.total_xp;
                }

                if (currentLevelEl && typeof data.level === 'number') {
                    currentLevelEl.textContent = data.level;
                }

                if (currentLevelXpEl && typeof data.current_level_xp === 'number') {
                    currentLevelXpEl.textContent = data.current_level_xp;
                }

                if (nextLevelXpEl && typeof data.next_level_xp === 'number') {
                    nextLevelXpEl.textContent = data.next_level_xp;
                }

                if (xpProgressBarEl && typeof data.xp_progress_percent === 'number') {
                    xpProgressBarEl.style.width = `${data.xp_progress_percent}%`;
                }

                if (typeof data.xp_delta === 'number') {
                    showXpToast(data.xp_delta);
                }

                if (data.level_up) {
                    showLevelUpToast(data.level_up);
                }
            } catch (error) {
                console.error('Toggle failed:', error);
            }

            updateEmptyStates();

            // const data = await postToggle(url);
            // moveCard(card, data.done);
        });
    </script>

    <style>
        @keyframes cardFadeInUp {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-enter {
            animation: cardFadeInUp 0.28s ease;
        }
    </style>
    @endpush
</x-app-layout>