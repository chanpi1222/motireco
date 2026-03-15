<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                Habits
            </h2>

            <button class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 active:bg-indigo-700 disabled:opacity-50 disabled:pointer-events-none">
                Add habit
            </button>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="mt-8 space-y-4">
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Morning stretch</h2>
                            <p class="mt-1 text-xs text-gray-500">5 minutes after waking up</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-gray-50 text-gray-700 ring-gray-200">
                            Todo
                        </span>
                    </div>
                    <div class="mt-4">
                        <button class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-gray-50 active:bg-gray-100">
                            Details
                        </button>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Read 10 pages</h2>
                            <p class="mt-1 text-xs text-gray-500">Evening wind-down routine</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-indigo-50 text-indigo-700 ring-indigo-200">
                            Doing
                        </span>
                    </div>
                    <div class="mt-4">
                        <button class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-gray-50 active:bg-gray-100">
                            Details
                        </button>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Drink water</h2>
                            <p class="mt-1 text-xs text-gray-500">8 cups throughout the day</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-green-50 text-green-700 ring-green-200">
                            Done
                        </span>
                    </div>
                    <div class="mt-4">
                        <button class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-gray-50 active:bg-gray-100">
                            Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>