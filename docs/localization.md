# 多語言與本地化功能

本文檔詳細說明 Laravel 部落格系統中的多語言功能實現和使用方法，特別是繁體中文的本地化支持。

## 1. 多語言架構概述

Laravel 內建了強大的本地化功能，允許網站輕鬆支持多種語言。本系統目前主要支持繁體中文，並保留了擴展到其他語言的靈活性。

### 1.1 核心組件

- **語言文件**：位於 `resources/lang/{語言代碼}` 目錄
- **翻譯輔助函數**：`__()`, `trans()`, `trans_choice()`
- **中介層**：`App\Http\Middleware\SetLocale` 設置當前語言

## 2. 語言文件結構

語言文件採用鍵值對形式，按功能模塊組織：

```
resources/lang/
├── en/                  # 英文 (默認)
│   ├── auth.php         # 認證相關文本
│   ├── pagination.php   # 分頁相關文本
│   ├── passwords.php    # 密碼相關文本
│   └── validation.php   # 表單驗證相關文本
├── zh-TW/               # 繁體中文
│   ├── auth.php
│   ├── pagination.php
│   ├── passwords.php
│   └── validation.php
```

## 3. 實現方式

### 3.1 語言文件定義

典型的語言文件 (`resources/lang/zh-TW/auth.php`) 結構如下：

```php
<?php

return [
    'failed' => '用戶名或密碼錯誤。',
    'password' => '密碼錯誤。',
    'throttle' => '登錄嘗試次數過多，請在 :seconds 秒後重試。',
];
```

### 3.2 語言切換機制

**語言中介層** (`app/Http/Middleware/SetLocale.php`)：

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * 處理傳入的請求
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        
        return $next($request);
    }
}
```

**語言切換控制器** (`app/Http/Controllers/LanguageController.php`)：

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * 切換語言
     */
    public function change(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|in:en,zh-TW',
        ]);
        
        Session::put('locale', $validated['locale']);
        
        return redirect()->back();
    }
}
```

**路由定義** (`routes/web.php`)：

```php
Route::post('/language', [LanguageController::class, 'change'])->name('language.change');
```

### 3.3 在視圖中使用

在 Blade 視圖中使用翻譯字符串：

```blade
<!-- 簡單翻譯 -->
<p>{{ __('auth.failed') }}</p>

<!-- 帶參數的翻譯 -->
<p>{{ __('auth.throttle', ['seconds' => 30]) }}</p>

<!-- 選擇性複數形式 -->
<p>{{ trans_choice('messages.apples', 10, ['count' => 10]) }}</p>
```

### 3.4 視圖文件本地化

對於直接在視圖文件中的靜態文本，我們採用直接翻譯的方式。例如將 `resources/views/auth/login.blade.php` 中的英文轉換為繁體中文：

原始英文版本：
```blade
<x-guest-layout>
    <h1>Log in to your account</h1>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </div>
        
        <!-- More form elements -->
        
        <x-primary-button>
            {{ __('Log in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
```

繁體中文版本：
```blade
<x-guest-layout>
    <h1>登入您的帳戶</h1>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <x-input-label for="email" :value="__('電子郵件')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </div>
        
        <!-- 更多表單元素 -->
        
        <x-primary-button>
            {{ __('登入') }}
        </x-primary-button>
    </form>
</x-guest-layout>
```

## 4. 實施本地化的最佳實踐

### 4.1 翻譯管理

- **保持一致性**：使用統一的術語表，確保相同概念在不同位置使用相同翻譯
- **避免硬編碼文本**：盡量使用語言文件和翻譯函數，而不是硬編碼文本
- **完整性檢查**：定期檢查是否有遺漏翻譯的文本

### 4.2 繁體中文特殊考量

- **字符編碼**：確保使用 UTF-8 編碼
- **日期格式**：對於日期時間格式，使用本地化的格式
- **貨幣符號**：根據地區使用正確的貨幣符號和格式
- **排序規則**：考慮中文特有的排序規則

### 4.3 日期與時間本地化

使用 Laravel 的 `Carbon` 擴展進行日期本地化：

```php
// 在控制器或模型中
$date = now()->locale('zh-TW')->isoFormat('YYYY年MM月DD日');

// 在視圖中直接使用
{{ $post->created_at->locale('zh-TW')->isoFormat('YYYY年MM月DD日') }}
```

## 5. 擴展到其他語言

若要添加新語言支持，按照以下步驟操作：

1. 創建新的語言目錄：`resources/lang/{新語言代碼}/`
2. 複製並翻譯現有的語言文件
3. 在語言切換控制器中添加新語言
4. 在用戶界面中添加語言選擇選項

## 6. 語言切換界面

在網站頂部或底部添加語言切換選項：

```blade
<div class="language-switcher">
    <form action="{{ route('language.change') }}" method="POST">
        @csrf
        <select name="locale" onchange="this.form.submit()">
            <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
            <option value="zh-TW" {{ app()->getLocale() == 'zh-TW' ? 'selected' : '' }}>繁體中文</option>
        </select>
    </form>
</div>
```

## 7. 驗證錯誤信息本地化

Laravel 的表單驗證錯誤信息也支持本地化：

```php
// resources/lang/zh-TW/validation.php
return [
    'required' => ':attribute 欄位是必填的。',
    'email' => ':attribute 必須是有效的電子郵件地址。',
    // 更多驗證規則...
    
    'attributes' => [
        'email' => '電子郵件',
        'password' => '密碼',
        // 更多欄位...
    ],
];
```

## 8. JSON 翻譯文件

對於簡單的字符串翻譯，也可以使用 JSON 格式的翻譯文件：

```json
// resources/lang/zh-TW.json
{
    "Welcome": "歡迎",
    "Log in": "登入",
    "Register": "註冊",
    "Forgot your password?": "忘記密碼？"
}
```

使用方式與 PHP 翻譯文件相同：

```blade
{{ __('Welcome') }}  <!-- 輸出：歡迎 -->
```

## 9. 常見問題與解決方案

### 9.1 翻譯未生效

**問題**：設置了翻譯但未顯示

**解決方案**：
- 檢查語言文件是否位於正確目錄
- 檢查語言代碼是否正確
- 清除視圖和配置緩存：
  ```bash
  php artisan view:clear
  php artisan config:clear
  ```

### 9.2 日期格式問題

**問題**：日期未正確本地化

**解決方案**：
- 確保使用 Carbon 的 locale 方法
- 檢查語言代碼是否支持 (zh-TW 而非 zh)

### 9.3 多語言 SEO 問題

**問題**：多語言內容的 SEO 優化

**解決方案**：
- 在 HTML 文檔中設置正確的 lang 屬性
- 使用 hreflang 元標記指示替代語言版本
- 確保每個語言版本有唯一的 URL

## 10. 相關資源

- [Laravel 本地化文檔](https://laravel.com/docs/localization)
- [Carbon 日期本地化文檔](https://carbon.nesbot.com/docs/#api-localization)
- [HTML 語言屬性指南](https://www.w3.org/International/questions/qa-html-language-declarations) 