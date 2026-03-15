<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <header class="bg-white border-b border-gray-100">
            <div class="max-w-5xl mx-auto px-4 h-14 flex justify-between items-center">
                <h1 class="text-sm font-semibold text-gray-500 tracking-wider">Motireco</h1>
                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                    + New
                </a>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 py-6 space-y-4">

            <article class="bg-white border border-gray-100 rounded-2xl p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">朝の読書</h2>
                        <p class="text-xs text-gray-500 mt-1">毎日 / 15分</p>
                    </div>
                    <span class="text-xs font-medium text-gray-500">Todo</span>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="#" class="text-xs text-indigo-600 hover:text-indigo-500">Details</a>
                </div>
            </article>

            <article class="bg-white border border-gray-100 rounded-2xl p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">プログラミング学習</h2>
                        <p class="text-xs text-gray-500 mt-1">平日 / 2時間</p>
                    </div>
                    <span class="text-xs font-medium text-gray-500">Doing</span>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="#" class="text-xs text-indigo-600 hover:text-indigo-500">Details</a>
                </div>
            </article>

            <article class="bg-white border border-indigo-200 rounded-2xl p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">筋トレ</h2>
                        <p class="text-xs text-gray-500 mt-1">月・水・金 / 30分</p>
                    </div>
                    <span class="text-xs font-medium text-indigo-600">Done</span>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="#" class="text-xs text-indigo-600 hover:text-indigo-500">Details</a>
                </div>
            </article>

        </main>
    </div>
</x-app-layout>