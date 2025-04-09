<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">所有文章</h1>
                        @auth
                            <a href="{{ route('posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                建立新文章
                            </a>
                        @endauth
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($posts->count() > 0)
                        <div class="space-y-6">
                            @foreach($posts as $post)
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                    <h2 class="text-2xl font-semibold mb-2">
                                        <a href="{{ route('posts.show', $post) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $post->title }}
                                        </a>
                                    </h2>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        <span>{{ $post->created_at->format('Y-m-d') }}</span> | 
                                        <span>作者: {{ $post->user->name }}</span> | 
                                        <span>分類: <a href="{{ route('categories.show', $post->category) }}" class="hover:underline">{{ $post->category->name }}</a></span>
                                    </div>
                                    <p class="mb-4">{{ $post->excerpt ?? Str::limit($post->content, 200) }}</p>
                                    <div class="flex space-x-4">
                                        <a href="{{ route('posts.show', $post) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            閱讀更多
                                        </a>
                                        @auth
                                            @if(Auth::id() === $post->user_id)
                                                <a href="{{ route('posts.edit', $post) }}" class="text-yellow-600 dark:text-yellow-400 hover:underline">
                                                    編輯
                                                </a>
                                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除這篇文章嗎？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">
                                                        刪除
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg text-center">
                            <p class="text-lg">暫無文章</p>
                            @auth
                                <a href="{{ route('posts.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    建立第一篇文章
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 