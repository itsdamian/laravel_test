<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <a href="{{ route('posts.show', $post) }}" class="text-blue-600 dark:text-blue-400 hover:underline">&laquo; 返回文章詳情</a>
                    </div>

                    <h1 class="text-3xl font-bold mb-6">編輯文章</h1>

                    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">標題</label>
                            <input type="text" name="title" id="title" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md @error('title') border-red-500 @enderror" value="{{ old('title', $post->title) }}" required>
                            @error('title')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">分類</label>
                            <select name="category_id" id="category_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md @error('category_id') border-red-500 @enderror" required>
                                <option value="">選擇分類</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">內容</label>
                            <textarea name="content" id="content" rows="10" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md @error('content') border-red-500 @enderror" required>{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">摘要 (可選)</label>
                            <textarea name="excerpt" id="excerpt" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md @error('excerpt') border-red-500 @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                            @error('excerpt')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">特色圖片 (可選)</label>
                            @if($post->featured_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="h-40 object-cover rounded">
                                    <div class="mt-1">
                                        <label for="remove_image" class="flex items-center text-sm">
                                            <input type="checkbox" name="remove_image" id="remove_image" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-red-600">刪除當前圖片</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="featured_image" id="featured_image" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md @error('featured_image') border-red-500 @enderror">
                            <p class="text-sm text-gray-500 mt-1">上傳新圖片將替換當前圖片</p>
                            @error('featured_image')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="published" class="flex items-center">
                                <input type="checkbox" name="published" id="published" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="1" {{ old('published', $post->published) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">已發布</span>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                儲存更改
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 