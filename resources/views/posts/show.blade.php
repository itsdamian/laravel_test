<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
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

                    <div class="mb-6">
                        <a href="{{ route('posts.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">&laquo; 返回文章列表</a>
                    </div>

                    <article>
                        <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>
                        
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <span>{{ $post->created_at->format('Y-m-d H:i') }}</span> | 
                            <span>作者: {{ $post->user->name }}</span> | 
                            <span>分類: <a href="{{ route('categories.show', $post->category) }}" class="hover:underline">{{ $post->category->name }}</a></span>
                        </div>

                        @auth
                            @if(Auth::id() === $post->user_id)
                                <div class="mb-6 space-x-4">
                                    <a href="{{ route('posts.edit', $post) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-sm">
                                        編輯文章
                                    </a>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除這篇文章嗎？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-sm">
                                            刪除文章
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        @if($post->featured_image)
                            <div class="mb-6">
                                <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="w-full max-h-96 object-cover rounded-lg">
                            </div>
                        @endif

                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </article>

                    <hr class="my-8 border-gray-200 dark:border-gray-700">

                    <section>
                        <h2 class="text-2xl font-bold mb-6">評論 ({{ $post->comments->count() }})</h2>
                        
                        @auth
                            <div class="mb-8">
                                <form action="{{ route('comments.store', $post) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">新增評論</label>
                                        <textarea name="content" id="content" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md @error('content') border-red-500 @enderror" placeholder="寫下你的評論...">{{ old('content') }}</textarea>
                                        @error('content')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        發表評論
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mb-8 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                                <p>要發表評論，請先 <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">登入</a> 或 <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 hover:underline">註冊</a>。</p>
                            </div>
                        @endauth

                        @if($post->comments->count() > 0)
                            <div class="space-y-6">
                                @foreach($post->comments as $comment)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" id="comment-{{ $comment->id }}">
                                        <div class="flex justify-between mb-2">
                                            <span class="font-semibold">{{ $comment->user->name }}</span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                        <p class="mb-2">{{ $comment->content }}</p>
                                        @auth
                                            @if(Auth::id() === $comment->user_id)
                                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除這條評論嗎？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 text-sm hover:underline">
                                                        刪除
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                                <p>暫無評論</p>
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 