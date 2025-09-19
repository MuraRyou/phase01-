<div>
    <!-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin -->
     <x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('User詳細') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <a href="{{ route('tweets.index') }}" class="text-blue-500 hover:text-blue-700 mr-2">一覧に戻る</a>
          <p class="text-gray-800 dark:text-gray-300 text-lg">{{ $user->name }}</p>
          <div class="text-gray-600 dark:text-gray-400 text-sm">
            <p>アカウント作成日時: {{ $user->created_at->format('Y-m-d H:i') }}</p>
          </div>
          @if ($user->id !== auth()->id())
          <div class="text-gray-900 dark:text-gray-100">
            @if ($user->followers->contains(auth()->id()))
            <form action="{{ route('follow.destroy', $user) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-500 hover:text-red-700">unFollow</button>
            </form>
            @else
            <form action="{{ route('follow.store', $user) }}" method="POST">
              @csrf
              <button type="submit" class="text-blue-500 hover:text-blue-700">follow</button>
            </form>
            @endif
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
</div>
