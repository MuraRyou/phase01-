
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
     <x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Tweet一覧') }}
    </h2>
  </x-slot>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


      @if ($currentTag)
            <div class="mb-4 p-4 bg-indigo-50 dark:bg-indigo-900 border-l-4 border-indigo-500 text-indigo-700 dark:text-indigo-200">
                <p class="font-bold">「#{{ $currentTag->name }}」で絞り込み中</p>
                <a href="{{ route('tweets.index') }}" class="text-sm underline hover:no-underline">→ 全てのツイートを表示</a>
            </div>
        @endif


      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          @foreach ($tweets as $tweet)
          <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
            <p class="text-gray-800 dark:text-gray-300">{{ $tweet->tweet }}</p>


             @if ($tweet->tags->isNotEmpty())
        <div class="mt-2 flex flex-wrap gap-2">
            @foreach ($tweet->tags as $tag)
                <a href="{{ route('tweets.index', ['tags' => $tag->id]) }}" 
                   class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-indigo-600 bg-indigo-200 uppercase last:mr-0 mr-1 hover:bg-indigo-300 transition duration-150 ease-in-out">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif


              <a href="{{ route('profile.show', $tweet->user) }}" class="block">
            <p class="text-gray-600 dark:text-gray-400 text-sm">投稿者: {{ $tweet->user->name }}</p>
             </a>
            <a href="{{ route('tweets.show', $tweet) }}" class="text-blue-500 hover:text-blue-700">詳細を見る</a>
             <div class="flex">
              @if ($tweet->liked->contains(auth()->id()))
              <form action="{{ route('tweets.dislike', $tweet) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700">dislike {{$tweet->liked->count()}}</button>
              </form>
              @else
              <form action="{{ route('tweets.like', $tweet) }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-500 hover:text-blue-700">like {{$tweet->liked->count()}}</button>
              </form>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

</x-app-layout>

