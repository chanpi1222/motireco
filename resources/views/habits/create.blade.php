<x-app-layout>
  {{-- アプリ全体で共通のレイアウトを使用（ヘッダー・ナビ等を統一） --}}

  <x-slot name="header">
    <div class="flex items-center justify-between">
      {{-- 画面タイトル --}}
      <h2 class="font-semibold text-xl text-slate-800 leading-tight">
        新しい習慣
      </h2>

      {{-- 一覧画面へ戻る導線 --}}
      <a href="{{ route('habits.index') }}" class="text-sm text-slate-600 hover:underline">
        戻る
      </a>
    </div>
  </x-slot>

  <div class="p-6 max-w-3xl mx-auto">
    {{-- フォーム全体のカードUI（視認性向上のため） --}}
    <div class="bg-white rounded-xl p-6 shadow-md">

      {{-- 習慣登録フォーム（POSTでstoreへ送信） --}}
      <form method="POST" action="{{ route('habits.store') }}">

        {{-- バリデーションエラー表示 --}}
        {{-- Controller側のvalidateで弾かれた内容を一覧表示 --}}
        @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
          <p class="text-sm font-medium text-red-800">入力内容を確認してください</p>
          <ul class="mt-2 list-disc pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
            {{-- 各エラーメッセージをリスト表示 --}}
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {{-- CSRF対策（Laravel必須） --}}
        @csrf

        {{-- ======================
            習慣名入力
        ====================== --}}
        <div>
          <label for="name" class="block text-sm font-medium text-slate-800">
            習慣名
          </label>

          <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name') }}"
            {{-- バリデーションエラー時に入力内容を保持 --}}
            placeholder="例：英語学習"
            class="mt-2 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:ring-blue-200">
        </div>

        {{-- ======================
            習慣詳細（任意）
        ====================== --}}
        <div class="mt-6">
          <label for="description" class="block text-sm font-medium text-slate-800">
            習慣の詳細(任意)
          </label>

          <textarea
            id="description"
            name="description"
            rows="3"
            placeholder="例:英語を10分学習する"
            class="mt-2 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:ring-blue-200">
          {{ old('description') }}
          {{-- バリデーションエラー時に入力内容を保持 --}}
          </textarea>
        </div>

        {{-- ======================
            ステータス選択
        ====================== --}}
        <div class="mt-6">

          {{-- old()優先で、初期値はtodoに設定 --}}
          {{-- フォーム再表示時に選択状態を維持するため --}}
          @php
          $current = old('status', 'todo');
          @endphp

          <select
            id="status"
            name="status"
            class="mt-2 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:ring-blue-200">

            {{-- 選択状態は @selected で制御 --}}
            <option value="todo" @selected($current==='todo' )>未着手</option>
            <option value="doing" @selected($current==='doing' )>進行中</option>
            <option value="done" @selected($current==='done' )>完了</option>

          </select>
        </div>

        {{-- ======================
            送信ボタン
        ====================== --}}
        <div class="mt-8 flex justify-end">
          <button
            type="submit"
            class="text-sm text-blue-600 hover:underline">
            保存
          </button>
        </div>

      </form>
    </div>
  </div>
</x-app-layout>