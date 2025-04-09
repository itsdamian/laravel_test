# 開發者指南

本文檔提供 Laravel 部落格系統的開發指南，包括項目架構、開發流程和最佳實踐。

## 項目架構

### MVC 架構

本項目遵循 Laravel 的 MVC（模型-視圖-控制器）架構：

1. **模型（Models）**：位於 `app/Models` 目錄下，代表數據庫表和業務邏輯。
2. **視圖（Views）**：位於 `resources/views` 目錄下，使用 Blade 模板引擎。
3. **控制器（Controllers）**：位於 `app/Http/Controllers` 目錄下，處理請求並返回響應。

### 目錄結構

```
├── app/                 # 應用程式核心代碼
│   ├── Http/           # 控制器、中介層和請求驗證
│   │   ├── Controllers/    # 控制器類
│   │   ├── Middleware/     # 中介層類
│   │   └── Requests/       # 表單請求驗證類
│   ├── Models/         # 數據模型類
│   └── Providers/      # 服務提供者
├── config/             # 配置文件
├── database/           # 數據庫相關文件
│   ├── factories/      # 模型工廠（用於生成測試數據）
│   ├── migrations/     # 數據庫遷移文件
│   └── seeders/        # 數據填充文件
├── docs/               # 文檔文件
├── public/             # 公開資源（入口文件和靜態資源）
├── resources/          # 原始資源文件
│   ├── css/            # CSS 文件
│   ├── js/             # JavaScript 文件
│   └── views/          # Blade 視圖模板
│       ├── auth/           # 認證相關視圖
│       ├── layouts/        # 布局模板
│       ├── posts/          # 文章相關視圖
│       └── partials/       # 可復用視圖片段
├── routes/             # 路由定義
│   ├── web.php             # Web 路由
│   └── api.php             # API 路由
├── storage/            # 應用程式生成的文件
├── tests/              # 測試文件
│   ├── Feature/            # 功能測試
│   └── Unit/               # 單元測試
└── vendor/             # 依賴包（由 Composer 管理）
```

## 開發流程

### 1. 環境設置

1. 克隆項目：
   ```bash
   git clone <repository-url>
   cd laravel-blog
   ```

2. 安裝依賴：
   ```bash
   composer install
   npm install
   ```

3. 配置環境：
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. 設置數據庫連接（在 `.env` 文件中）：
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_blog
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. 遷移數據庫：
   ```bash
   php artisan migrate --seed
   ```

### 2. 開發新功能

1. 創建新分支：
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. 添加新的路由（在 `routes/web.php` 或 `routes/api.php`）：
   ```php
   Route::get('/your-route', [YourController::class, 'method'])->name('route.name');
   ```

3. 創建或更新控制器：
   ```bash
   php artisan make:controller YourController
   ```

4. 創建或更新模型：
   ```bash
   php artisan make:model YourModel -m  # -m 參數會同時創建遷移文件
   ```

5. 創建或更新視圖（在 `resources/views/` 目錄下）：
   ```php
   <x-app-layout>
       <!-- 你的視圖內容 -->
   </x-app-layout>
   ```

6. 編寫測試：
   ```bash
   php artisan make:test YourFeatureTest
   ```

7. 運行測試：
   ```bash
   php artisan test
   ```

### 3. 部署

1. 優化自動加載：
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

2. 優化配置加載：
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. 編譯前端資源：
   ```bash
   npm run build
   ```

4. 使用 Docker 部署（或其他部署方法）：
   ```bash
   docker-compose up -d
   ```

## 最佳實踐

### 代碼風格

- 遵循 PSR-12 代碼風格標準
- 使用清晰、描述性的變量和函數名稱
- 添加適當的註釋和文檔
- 使用 Laravel 內置的輔助函數和工具

### 資料庫

- 使用遷移文件管理數據庫結構
- 為所有表格添加適當的索引
- 使用外鍵建立表格之間的關係
- 在模型中定義關係和約束

### 安全性

- 使用 Laravel 的表單請求驗證來驗證輸入
- 防止跨站腳本攻擊（XSS）和跨站請求偽造（CSRF）
- 使用授權策略控制資源訪問
- 避免將敏感信息寫入日誌或顯示給用戶

### 性能

- 使用緩存減少數據庫查詢
- 在適當的地方使用延遲加載和預加載關係
- 使用隊列處理耗時的任務
- 定期監控應用程式性能

## 常見問題解決

### 資料庫遷移失敗

問題：遷移失敗，出現外鍵約束錯誤。
解決：確保遷移文件按正確順序執行，先創建被引用的表格。

### 路由找不到

問題：訪問路由出現 404 錯誤。
解決：
1. 檢查路由定義是否正確
2. 運行 `php artisan route:clear` 清除路由緩存
3. 檢查 `routes/web.php` 文件中的路由定義

### 視圖渲染錯誤

問題：渲染視圖時出現錯誤。
解決：
1. 檢查視圖文件是否存在
2. 確保傳遞了視圖所需的所有變量
3. 運行 `php artisan view:clear` 清除視圖緩存

## 有用的資源

- [Laravel 官方文檔](https://laravel.com/docs)
- [Laravel 新手教程](https://laracasts.com/series/laravel-8-from-scratch)
- [Tailwind CSS 文檔](https://tailwindcss.com/docs)
- [PHP PSR 標準](https://www.php-fig.org/psr/) 