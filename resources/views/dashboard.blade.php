<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Dashboard</h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('habits.index') }}" class="text-sm text-blue-600 hover:underline">
                    習慣一覧へ
                </a>

                <a href="{{ route('habits.create') }}"
                    class="inline-flex items-center rounded-lg bg-blue-600 p-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    習慣を追加
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 p-6 max-w-3xl mx-auto">
        @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success')}}
        </div>
        @endif

        @if(session('level_up'))
        @php
        $lvFrom = data_get(session('level_up'), 'from');
        $lvTo = data_get(session('level_up'), 'to');
        @endphp

        <div id="levelUpBanner" class="levelup-toast">
            <div class="levelup-card">
                <div class="level-title">🎉 LEVEL UP!</div>
                <div class="levelup-text">
                    Lv.{{ $lvFrom}} → Lv.{{ $lvTo }} になりました!
                </div>
                <div class="levelup-sub">次も頑張ろう!</div>
            </div>
        </div>
        @endif

        @if(session('xp_delta'))
        <div id="xpToast" class="xp-toast">
            <div class="xp-card {{ session('xp_delta') > 0 ? 'xp-plus' : 'xp-minus' }}">
                {{ session('xp_delta') > 0 ? '✨ XP +' . session('xp_delta') : '↩ XP ' . session('xp_delta') }}
            </div>
        </div>
        @endif


        {{-- 未達（今日やること） --}}
        <div class="mt-6" id="pendingList">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">今日やること(未達)</h3>

            @forelse ($pendingHabits as $habit)
            <div class="bg-white rounded-xl p-4 shadow mb-3 border border-slate-200 border-l-4 border-l-blue-500 hover:shadow-md transition"
                data-habit-card
                data-habit-id="{{ $habit->id }}"
                data-toggle-url="{{ route('habits.logs.toggle', ['habit' => $habit->id ])}}"
                data-state="pending">

                <p class="text-slate-800 text-sm font-medium">{{ $habit->name }}</p>
                <p class="text-blue-600 text-xs mt-1">● 未完了</p>
                <p class="text-slate-500 text-xs mt-1">連続:{{ $habit->streak_today }} 日</p>

                <button type="button" class="js-toggle mt-3 inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                    今日は完了
                </button>

            </div>
            @empty
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-slate-600 text-sm">今日の未達はありません 🎉</p>
            </div>
            @endforelse
        </div>

        {{-- 完了済み --}}
        <div class="mt-8" id="doneList">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">今日の完了済み</h3>

            @forelse ($doneHabits as $habit)
            <div class="bg-white rounded-xl p-4 shadow mb-3 border border-slate-200 border-l-4 border-l-green-500"
                data-habit-card
                data-habit-id="{{ $habit->id }}"
                data-toggle-url="{{ route('habits.logs.toggle', ['habit' => $habit->id ])}}"
                data-state="done">

                <p class="text-slate-800 text-sm font-medium">{{ $habit->name }}</p>
                <p class="text-green-600 text-xs mt-1 font-semibold">✓ 今日完了</p>
                <p class="text-slate-500 text-xs mt-1">連続:{{ $habit->streak_today }} 日</p>

                @if($habit->habit_title)
                <span class="inline-block mt-1 text-xs px-2 py-1 rounded bg-amber-100 text-amber-700">
                    {{ $habit->habit_title }}
                </span>
                @endif

                <button type="button" class="js-toggle mt-3 inline-flex items-center rounded-lg bg-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-300">
                    取り消す
                </button>

            </div>
            @empty
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-slate-600 text-sm">完了した習慣はありません。</p>
            </div>
            @endforelse
        </div>

    </div>

    @push('scripts')
    <script>
        // LEVEL UP バナー：表示→3.0秒後にフェードアウト
        const banner = document.getElementById('levelUpBanner');
        if (banner) {
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(6px)';
            banner.style.transition = 'all .35s ease';

            requestAnimationFrame(() => {
                banner.style.opacity = "1";
                banner.style.transform = 'translateY(0)';
            });

            // ✅ 表示を長めに（3秒）
            setTimeout(() => {
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-6px)';
            }, 3000);

            // ✅ フェードの後に消す（+0.5秒）
            setTimeout(() => {
                banner.remove();
            }, 3500);
        }
    </script>

    <script>
        // XP トースト：表示→1.2秒後にフェードアウト（右上）
        const xpToast = document.getElementById('xpToast');
        if (xpToast) {
            xpToast.style.opacity = '0';
            xpToast.style.transform = 'translateY(6px)';
            xpToast.style.transition = 'all .25s ease';

            requestAnimationFrame(() => {
                xpToast.style.opacity = "1";
                xpToast.style.transform = 'translateY(0)';
            });

            setTimeout(() => {
                xpToast.style.opacity = "0";
                xpToast.style.transform = 'translateY(-6px)';
            }, 1200);

            setTimeout(() => {
                xpToast.remove();
            }, 1500);
        }
    </script>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";

        function showXpToast(delta) {
            const wrap = document.createElement('div');
            wrap.className = 'xp-toast';
            wrap.innerHTML = `
            <div class="xp-card ${delta > 0 ? 'xp-plus' : 'xp-minus'}">
                ${delta > 0 ? '✨ XP +' + delta : '↩ XP ' + delta}
            </div>
        `;

            document.body.appendChild(wrap);

            wrap.style.opacity = '0';
            wrap.style.transform = 'translateY(6px)';
            wrap.style.transition = 'all .25s ease';

            requestAnimationFrame(() => {
                wrap.style.opacity = '1';
                wrap.style.transform = 'translateY(0)';
            });

            setTimeout(() => {
                wrap.style.opacity = '0';
                wrap.style.transform = 'translateY(-6px)';
            }, 1200);

            setTimeout(() => {
                wrap.remove();
            }, 1500);
        }

        function showLevelUpBanner(from, to) {
            const wrap = document.createElement('div');
            wrap.className = 'levelup-toast';
            wrap.innerHTML = `
            <div class="levelup-card">
                <div class="level-title">🎉 LEVEL UP!</div>
                <div class="levelup-text">Lv.${from} → Lv.${to} になりました!</div>
                <div class="levelup-sub">次も頑張ろう!</div>
            </div>
        `;

            document.body.appendChild(wrap);

            wrap.style.opacity = '0';
            wrap.style.transform = 'translateY(6px)';
            wrap.style.transition = 'all .35s ease';

            requestAnimationFrame(() => {
                wrap.style.opacity = '1';
                wrap.style.transform = 'translateY(0)';
            });

            setTimeout(() => {
                wrap.style.opacity = '0';
                wrap.style.transform = 'translateY(-6px)';
            }, 3000);

            setTimeout(() => {
                wrap.remove();
            }, 3500);
        }

        async function postToggle(url) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            });

            if (!response.ok) {
                throw new Error('通信に失敗しました');
            }

            return await response.json();
        }

        function moveCard(card, done) {
            const pendingList = document.getElementById('pendingList');
            const doneList = document.getElementById('doneList');
            const button = card.querySelector('.js-toggle');

            if (!pendingList || !doneList || !button) return;

            if (done) {
                card.dataset.state = 'done';
                card.classList.remove('border-l-blue-500');
                card.classList.add('border-l-green-500');

                const statusText = card.querySelector('.text-blue-600');
                if (statusText) {
                    statusText.classList.remove('text-blue-600');
                    statusText.classList.add('text-green-600', 'font-semibold');
                    statusText.textContent = '✓ 今日完了';
                }

                button.textContent = '取り消す';
                button.className = 'js-toggle mt-3 inline-flex items-center rounded-lg bg-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-300';

                doneList.appendChild(card);
            } else {
                card.dataset.state = 'pending';
                card.classList.remove('border-l-green-500');
                card.classList.add('border-l-blue-500');

                const statusText = card.querySelector('.text-green-600');
                if (statusText) {
                    statusText.classList.remove('text-green-600', 'font-semibold');
                    statusText.classList.add('text-blue-600');
                    statusText.textContent = '● 未完了';
                }

                button.textContent = '今日は完了';
                button.className = 'js-toggle mt-3 inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700';

                pendingList.appendChild(card);
            }
        }

        console.log('toggle script loaded');

        document.addEventListener('click', async (e) => {
            console.log('click detected');

            const button = e.target.closest('.js-toggle');
            if (!button) return;

            const card = button.closest('[data-habit-card]');
            if (!card) return;

            const url = card.dataset.toggleUrl;
            if (!url) return;

            button.disabled = true;

            try {
                const data = await postToggle(url);

                moveCard(card, data.done);

                if (data.xp_delta) {
                    showXpToast(data.xp_delta);
                }

                if (data.level_up && data.level_up.from && data.level_up.to) {
                    showLevelUpBanner(data.level_up.from, data.level_up.to);
                }
            } catch (error) {
                console.error(error);
                alert(error.message || 'エラーが発生しました');
            } finally {
                button.disabled = false;
            }
        });
    </script>

    <style>
        @keyframes shine {
            0% {
                transform: translateX(-120%) skewX(-20deg);
                opacity: 0;
            }

            15% {
                opacity: .9;
            }

            100% {
                transform: translateX(260%) skewX(-20deg);
                opacity: 0;
            }
        }

        .levelup-toast {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* 中央 */
            z-index: 9999;
            pointer-events: none;
        }

        .levelup-card {
            min-width: 260px;
            max-width: 340px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #111827;
            color: #fff;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .18);
            overflow: hidden;
            position: relative;
        }

        .levelup-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 0%, rgba(255, 255, 255, .22) 45%, transparent 60%);
            animation: shine 1.1s ease-out 1;
        }

        .level-title {
            font-weight: 800;
            letter-spacing: .02em;
        }

        .levelup-text {
            margin-top: 4px;
            font-size: 14px;
            opacity: .95;
        }

        .levelup-sub {
            margin-top: 2px;
            font-size: 12px;
            opacity: .75;
        }

        .xp-toast {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9998;
            pointer-events: none;
        }

        .xp-card {
            min-width: 180px;
            padding: 10px 14px;
            border-radius: 14px;
            color: #fff;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .18);
        }

        .xp-plus {
            background: #2563eb;
        }

        .xp-minus {
            background: #6b7280;
        }
    </style>
    @endpush
</x-app-layout>