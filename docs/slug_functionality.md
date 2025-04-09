# URL Slug 功能說明

## 概述

本文檔說明 Laravel 部落格系統中 URL Slug 功能的實現和使用方法。Slug 是用於 URL 中的人類可讀字串，比數字 ID 更易於理解和搜索引擎優化(SEO)。

## 實現方式

### 數據庫結構

在 `posts` 表中，我們添加了 `slug` 字段來存儲每個文章的唯一識別符：

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->text('excerpt')->nullable();
    $table->string('slug')->unique(); // 唯一的 slug 字段
    $table->string('featured_image')->nullable();
    $table->boolean('published')->default(false);
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

### 模型設定

在 `Post` 模型中，我們將 `slug` 添加到 `$fillable` 屬性中，允許批量賦值：

```php
protected $fillable = [
    'title',
    'content',
    'excerpt',
    'slug',
    'featured_image',
    'published',
    'user_id',
    'category_id'
];
```

### 自動生成 Slug

當文章創建或更新時，我們在控制器中使用 Laravel 的 `Str::slug()` 方法自動生成 slug。以下是 `PostController` 中相關代碼的摘錄：

```php
// 在創建新文章時
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        // ...其他驗證規則
    ]);

    $validated['user_id'] = Auth::id();
    $validated['slug'] = Str::slug($validated['title']);
    
    // ...處理圖片和其他數據
    
    Post::create($validated);
    // ...
}

// 在更新文章時
public function update(Request $request, Post $post)
{
    // ...
    $validated = $request->validate([
        'title' => 'required|max:255',
        // ...其他驗證規則
    ]);

    $validated['slug'] = Str::slug($validated['title']);
    
    // ...處理圖片和其他數據
    
    $post->update($validated);
    // ...
}
```

### 模型工廠中的 Slug 生成

在測試和開發環境中，我們使用模型工廠生成測試數據。在 `PostFactory` 中，我們也確保為每個模擬文章生成有效的 slug：

```php
public function definition(): array
{
    $title = $this->faker->sentence;
    return [
        'title' => $title,
        'content' => $this->faker->paragraphs(3, true),
        'excerpt' => $this->faker->paragraph,
        'slug' => Str::slug($title),
        'featured_image' => null,
        'published' => true,
        'user_id' => User::factory(),
        'category_id' => Category::factory(),
    ];
}
```

## 使用方法

### 在控制器中

當使用路由模型綁定時，Laravel 默認會用主鍵(ID)查找模型。如果你想使用 slug 而不是 ID，可以在 `Post` 模型中覆蓋 `getRouteKeyName()` 方法：

```php
public function getRouteKeyName()
{
    return 'slug';
}
```

然後，你的路由可以接收 slug 並找到正確的文章：

```php
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
```

### 在視圖中

當生成 URL 或連結到文章時，不需要顯式指定 slug。如果已經實現了 `getRouteKeyName()` 方法，Laravel 會自動使用文章的 slug 屬性：

```php
<a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
```

如果沒有覆蓋 `getRouteKeyName()`，則需要顯式傳遞 slug：

```php
<a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
```

## 注意事項

1. **唯一性**：每個文章的 slug 必須是唯一的。如果標題重複，可能需要添加額外的邏輯來確保 slug 的唯一性，例如添加日期或隨機字串。

2. **特殊字符處理**：`Str::slug()` 方法會自動處理特殊字符、空格和非英文字符，將它們轉換為 URL 友好的格式。

3. **更新後的 URL 變化**：如果允許編輯文章標題，slug 可能會隨之改變，導致先前的 URL 失效。根據需求，可能需要實現額外的邏輯來維護歷史 URL 或者固定 slug 不變。

4. **搜索引擎優化**：使用 slug 而不是 ID 可以提升搜索引擎排名，因為 URL 中包含了關鍵詞。

## 進一步改進建議

1. **持久化 Slug**：一旦創建了 slug，考慮將其保持不變，即使標題更改。這有助於維護連結的持久性。

2. **自定義 Slug**：允許用戶自定義 slug，而不僅僅是自動從標題生成。

3. **Slug 歷史記錄**：維護歷史 slug 表，實現對舊 URL 的重定向，確保舊鏈接不會失效。 