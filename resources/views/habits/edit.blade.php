<x-app-layout>
  {{-- アプリ共通レイアウトを使用（ナビやヘッダーを統一するため） --}}

  <x-slot name="header">
    <div class="flex items-center justify-between">
      {{-- 画面タイトル：編集画面であることを明示 --}}
      <h2 class="font-semibold text-xl text-slate-800 leading-tight">
        習慣を編集
      </h2>

      {{-- 一覧画面へ戻る導線（編集キャンセル時のUX確保） --}}
      <a href="{{ route('habits.index') }}" class="text-slate-600 hover:underline">
        戻る
      </a>
    </div>
  </x-slot>

  <div class="p-6 max-w-3xl mx-auto">
    {{-- フォーム全体をカードUIで囲み、視認性を向上 --}}
    <div class="bg-white rounded-xl p-6 shadow-md">

      {{-- 習慣更新用フォーム --}}
      <form method="POST" action="{{ route('habits.update', $habit) }}">
        {{-- CSRF対策（Laravel必須） --}}
        @csrf

        {{-- HTMLフォームではPATCHが使えないためメソッド偽装 --}}
        @method('PATCH')

        {{-- バリデーションエラーがある場合のみ表示 --}}
        @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
          <p class="text-sm font-medium text-red-800">入力内容を確認してください</p>

          {{-- 複数エラーを一覧表示（ユーザーに修正箇所を明示） --}}
          <ul class="mt-2 list-disc pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <div>
          <label for="name" class="block text-sm font-medium text-slate-800">
            習慣名
          </label>

          {{--
            old() を優先することで
            ・バリデーションエラー時に入力値を保持
            ・通常時は既存データを表示
          --}}
          <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $habit->name) }}"
            class="mt-2 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:ring-blue-200">
        </div>

        @php
        // 選択状態を維持するための現在値
        // old() を優先し、エラー時の入力内容を保持する
        $current = old('status', $habit->status);
        @endphp

        <div class="mt-6">
          <label for="status" class="block text-sm font-medium text-slate-800">
            状態
          </label>

          {{-- 状態選択：todo / doing / done --}}
          <select
            id="status"
            name="status"
            class="mt-2 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:ring-blue-200">
            {{-- @selected により現在の値と一致したものを選択状態にする --}}
            <option value="todo" @selected($current==='todo' )>未着手</option>
            <option value="doing" @selected($current==='doing' )>進行中</option>
            <option value="done" @selected($current==='done' )>完了</option>
          </select>
        </div>

        <div class="mt-8 flex justify-end">
          {{-- 更新ボタン：シンプルに送信のみ（JS依存を避ける） --}}
          <button type="submit" class="text-sm text-blue-600 hover:underline">
            更新
          </button>
        </div>

      </form>
    </div>
  </div>
</x-app-layout>