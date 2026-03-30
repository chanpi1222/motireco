{{-- アプリ全体のレイアウト（ヘッダー・ナビなどを共通化） --}}
<x-app-layout>

  {{-- ヘッダー部分：ページタイトル --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      マイページ
    </h2>
  </x-slot>

  {{-- メインコンテンツエリア --}}
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- 統計カード一覧（グリッド表示） --}}
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">

        {{-- 今日の完了件数 --}}
        {{-- Controllerから渡された $todayCompletedCount を表示 --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">今日の完了件数</p>
          <p class="text-2xl font-bold text-blue-600">
            {{ $todayCompletedCount }} 件
          </p>
        </div>

        {{-- 今日の獲得XP --}}
        {{-- 1件あたりのXPと合計XPを表示 --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">
            今日の獲得XP (+{{ $xpPerDone }} / 件)
          </p>
          <p class="text-2xl font-bold text-indigo-600">
            {{ $todayXp }} XP
          </p>
        </div>

        {{-- 今月の累計XP --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">今月の累計XP</p>
          <p class="text-2xl font-bold text-indigo-600">
            {{ $monthlyXp }} XP
          </p>
        </div>

        {{-- レベル表示 + 進捗バー --}}
        {{-- XPの進捗率を元にプログレスバーを描画 --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">レベル</p>

          {{-- 現在のレベル --}}
          <p class="text-2xl font-bold text-purple-600">
            Lv.{{ $level }}
          </p>

          {{-- レベル進捗バー --}}
          {{-- xpProgressPercent（%）で幅を制御 --}}
          <div class="mt-2 bg-slate-200 rounded-full h-2">
            <div
              class="bg-purple-500 h-2 rounded-full"
              style="width: {{ $xpProgressPercent }}%">
            </div>
          </div>

          {{-- 次レベルまでの必要XP表示 --}}
          <p class="text-xs text-slate-500 mt-2">
            次のLvまで {{ $nextLevelXp - $currentLevelXp }} XP
            ({{ $currentLevelXp }}/{{ $nextLevelXp }})
          </p>
        </div>

        {{-- 全体の連続達成日数（ストリーク） --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">連続達成 (全体)</p>
          <p class="text-2xl font-bold text-blue-600">
            {{ $globalStreak }} 日
          </p>
        </div>

        {{-- 現在の称号 --}}
        {{-- タイトル一覧ページへのリンクも設置 --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">称号</p>
          <p class="text-xl font-bold text-slate-800">
            {{ $title }}
          </p>

          <a href="{{ route('titles.index') }}"
            class="mt-3 inline-block text-sm text-blue-600 hover:underline">
            称号一覧を見る
          </a>
        </div>

        {{-- 今月の完了日数 --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">今月の完了日数</p>
          <p class="text-2xl font-bold text-blue-600">
            {{ $monthlyCompletedDays }} 日
          </p>
        </div>

        {{-- 今月の達成率 --}}
        {{-- Controller側で計算済みの値を表示のみ --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">今月の達成率</p>
          <p class="text-2xl font-bold text-emerald-600">
            {{ $monthlyRate }} %
          </p>
        </div>

        {{-- 総XP --}}
        <div class="bg-white rounded-xl p-4 shadow sm:rounded-lg">
          <p class="text-sm text-slate-500">総XP</p>
          <p class="text-2xl font-bold text-indigo-700">
            {{ $totalXp }} XP
          </p>
        </div>

      </div>

      {{-- プロフィール更新フォーム --}}
      {{-- Laravel Breeze のコンポーネントを読み込み --}}
      <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
          @include('profile.partials.update-profile-information-form')
        </div>
      </div>

      {{-- パスワード変更フォーム --}}
      <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
          @include('profile.partials.update-password-form')
        </div>
      </div>

      {{-- アカウント削除フォーム --}}
      <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
          @include('profile.partials.delete-user-form')
        </div>
      </div>

    </div>
  </div>

</x-app-layout>