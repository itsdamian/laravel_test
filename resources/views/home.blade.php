<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold mb-4">歡迎來到我的部落格</h1>
                        <p class="text-lg">這是一個用 Laravel 框架構建的簡易部落格系統。</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h2 class="text-2xl font-semibold mb-4">最新文章</h2>
                            
                            @if($posts->count() > 0)
                                <div class="space-y-6">
                                    @foreach($posts as $post)
                                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                            <h3 class="text-xl font-semibold mb-2">
                                                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ $post->title }}
                                                </a>
                                            </h3>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                                <span>{{ $post->created_at->format('Y-m-d') }}</span> | 
                                                <span>分類: <a href="{{ route('categories.show', $post->category) }}" class="hover:underline">{{ $post->category->name }}</a></span>
                                            </div>
                                            <p class="mb-4">{{ $post->excerpt ?? Str::limit($post->content, 150) }}</p>
                                            <a href="{{ route('posts.show', $post) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                閱讀更多 &raquo;
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-6">
                                    {{ $posts->links() }}
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                                    <p>暫無文章</p>
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                <h2 class="text-xl font-semibold mb-4">分類</h2>
                                
                                @if($categories->count() > 0)
                                    <ul class="space-y-2">
                                        @foreach($categories as $category)
                                            <li>
                                                <a href="{{ route('categories.show', $category) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>暫無分類</p>
                                @endif
                            </div>
                            
                            @auth
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm mt-6">
                                    <h2 class="text-xl font-semibold mb-4">管理</h2>
                                    <ul class="space-y-2">
                                        <li>
                                            <a href="{{ route('posts.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                建立新文章
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('categories.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                建立新分類
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
