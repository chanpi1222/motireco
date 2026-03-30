{{-- ナビゲーション全体
    Alpine.jsでレスポンシブメニュー（ハンバーガー）の開閉状態を管理 --}}
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">

    {{-- 最大幅を制御して中央寄せ（レスポンシブ対応） --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ナビバーの高さ固定＆左右レイアウト --}}
        <div class="flex justify-between h-16">

            {{-- 左側：ロゴ＋メインナビ --}}
            <div class="flex">

                {{-- ロゴ：クリックでダッシュボードへ遷移（ホーム的役割） --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- アプリロゴコンポーネント --}}
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                {{-- PC表示用ナビゲーション（sm以上で表示） --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    {{-- 現在のルートと一致したらactive状態にする --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- logs配下のルートならアクティブ --}}
                    <x-nav-link :href="route('logs.index')" :active="request()->routeIs('logs.*')">
                        ログ
                    </x-nav-link>

                    {{-- analytics配下 --}}
                    <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')">
                        分析
                    </x-nav-link>

                    {{-- titles配下 --}}
                    <x-nav-link :href="route('titles.index')" :active="request()->routeIs('titles.*')">
                        称号
                    </x-nav-link>

                    {{-- ユーザープロフィール --}}
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                        マイページ
                    </x-nav-link>
                </div>
            </div>

            {{-- 右側：ユーザー情報＋ドロップダウン --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                {{-- 共通ドロップダウンコンポーネント --}}
                <x-dropdown align="right" width="48">

                    {{-- トリガー（クリックでメニュー表示） --}}
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">

                            {{-- ユーザー名＋称号表示 --}}
                            <div class="flex items-center gap-2">

                                {{-- ログインユーザー名 --}}
                                <span>{{ Auth::user()->name }}</span>

                                {{-- 現在の称号（存在する場合のみ表示） --}}
                                <span class="text-xs text-slate-500">
                                    {{ $title ?? '' }}
                                </span>
                            </div>

                            {{-- ドロップダウンアイコン --}}
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    {{-- ドロップダウン内容 --}}
                    <x-slot name="content">

                        {{-- プロフィール画面 --}}
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        {{-- ログアウト（POST必須のためフォームで送信） --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            {{-- aタグ風UIだが実際はform submit --}}
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                         this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- スマホ用ハンバーガーメニュー --}}
            <div class="-me-2 flex items-center sm:hidden">

                {{-- openのtrue/falseでメニュー開閉 --}}
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">

                    {{-- メニューアイコン --}}
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">

                        {{-- 開いていない時（三本線） --}}
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        {{-- 開いている時（×） --}}
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- スマホ用メニュー本体 --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        {{-- ナビリンク --}}
        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        {{-- ユーザー情報 --}}
        <div class="pt-4 pb-1 border-t border-gray-200">

            <div class="px-4">
                {{-- 名前 --}}
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>

                {{-- メール --}}
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            {{-- メニュー --}}
            <div class="mt-3 space-y-1">

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                {{-- ログアウト --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                 this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>