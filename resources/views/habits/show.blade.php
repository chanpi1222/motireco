{{--
  x-app-layout:
  アプリ全体で共通のレイアウト（ヘッダー・余白・スタイル）を適用
  → レイアウトの統一と再利用性を担保するため
--}}
<x-app-layout>

  {{--
    headerスロット:
    各ページごとにヘッダー内容を差し替えるための領域
    → レイアウトは共通、内容だけ個別に制御する設計
  --}}
  <x-slot name="header">

    {{-- ページタイトル（習慣詳細画面） --}}
    <h2 class="font-semibold text-xl text-slate-800 leading-tight">
      習慣詳細
    </h2>

    {{--
      ナビゲーションエリア:
      一覧へ戻る / 編集画面への導線
      → CRUD操作の回遊性を高めるため
    --}}
    <div class="flex items-center gap-3">

      {{-- 一覧画面へ戻るリンク --}}
      <a href="{{ route('habits.index') }}" class="text-sm text-slate-600 hover:underline">
        一覧へ戻る
      </a>

      {{--
        編集画面へのリンク
        $habit をそのまま渡すことで、ルートモデルバインディングを利用
        → IDを明示せずシンプルに記述できる
      --}}
      <a href="{{ route('habits.edit', $habit) }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
        編集する
      </a>

    </div>
  </x-slot>

  {{--
    メインコンテンツ領域:
    max-w-3xlで横幅を制限し、可読性を担保
    → UIの一貫性（カードレイアウト）を維持
  --}}
  <div class="p-6 max-w-3xl mx-auto">

    {{--
      カードUI:
      習慣の詳細情報を視覚的に区切る
    --}}
    <div class="bg-white rounded-xl p-6 shadow">

      {{-- 習慣名（メイン情報） --}}
      <p class="text-lg font-bold text-slate-800">
        {{ $habit->name }}
      </p>

      {{--
        ステータス表示:
        status_label はモデル側で整形済みの値を使用
        → View側でロジックを書かない設計（責務分離）
      --}}
      <p class="mt-3 text-sm text-slate-500">
        ステータス: {{ $habit->status_label }}
      </p>

    </div>
  </div>

</x-app-layout>