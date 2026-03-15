<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            マイページ
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">今日の完了件数</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $todayCompletedCount }} 件</p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">今日の獲得XP (+{{ $xpPerDone }} / 件)</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $todayXp }} XP</p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">今月の累計XP</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $monthlyXp }} XP</p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">レベル</p>
                    <p class="text-2xl font-bold text-purple-600">Lv.{{ $level }}</p>

                    <div class="mt-2 bg-slate-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $xpProgressPercent }}%"></div>
                    </div>

                    <p class="text-xs text-slate-500 mt-2">
                        次のLvまで {{ $nextLevelXp - $currentLevelXp }} XP
                        ({{ $currentLevelXp }}/{{ $nextLevelXp }})
                    </p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">連続達成 (全体) </p>
                    <p class="text-2xl font-bold text-blue-600">{{ $globalStreak }} 日</p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">称号</p>
                    <p class="text-xl font-bold text-slate-800">{{ $title }}</p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">今月の完了日数</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $monthlyCompletedDays }} 日</p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">今月の達成率</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $monthlyRate }} %</p>
                </div>

                <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
                    <p class="text-sm text-slate-500">総XP</p>
                    <p class="text-2xl font-bold text-indigo-700">{{ $totalXp }} XP</p>
                </div>

            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>