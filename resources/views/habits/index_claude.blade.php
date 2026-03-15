<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto px-6 py-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Habits</h1>
                <a href="#" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    + New Habit
                </a>
            </div>
        </header>

        <!-- Habits List -->
        <main class="max-w-4xl mx-auto px-6 py-12">
            <div class="space-y-6">
                <!-- Habit Card 1 -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Morning meditation</h3>
                            <p class="text-sm text-gray-500">10 minutes daily practice</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Active</span>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700">Details →</a>
                    </div>
                </div>

                <!-- Habit Card 2 -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Read before bed</h3>
                            <p class="text-sm text-gray-500">At least 20 pages every night</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Active</span>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700">Details →</a>
                    </div>
                </div>

                <!-- Habit Card 3 -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Drink water</h3>
                            <p class="text-sm text-gray-500">8 glasses throughout the day</p>
                        </div>
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Paused</span>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700">Details →</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>