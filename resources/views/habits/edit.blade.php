<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-slate-800 leading-tight">
        習慣を編集
      </h2>

      <a href="{{ route('habits.index') }}" class="text-slate-600 hover:underline">
        戻る
      </a>
    </div>
  </x-slot>

  <div class="p-6 max-w-3xl mx-auto">
    <div class="bg-white rounded-xl p-6 shadow-md">
      <form method="POST" action="{{ route('habits.update', $habit) }}">
        @csrf
        @method('PATCH')

        @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
          <p class="text-sm font-medium text-red-800">入力内容を確認してください</p>
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
          <input id="name" name="name" type="text" value="{{ old('name', $habit->name) }}" class="mt-2 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:ring-blue-200">
        </div>

        @php
        $current = old('status', $habit->status);
        @endphp

        <div class="mt-6">
          <label for="status" class="block text-sm font-medium text-slate-800">
            状態
          </label>
          <select id="status" name="status" class="mt-2 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:borderblue-400 focus:ring-blue-200">
            <option value="todo" @selected($current==='todo' )>未着手</option>
            <option value="doing" @selected($current==='doing' )>進行中</option>
            <option value="done" @selected($current==='done' )>完了</option>
          </select>
        </div>

        <div class="mt-8 flex justify-end">
          <button type="submit" class="text-sm text-blue-600 hover:underline">
            更新
          </button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>