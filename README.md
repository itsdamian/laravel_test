# Laravel 部落格系統

這是一個基於 Laravel 框架開發的部落格系統，提供文章發布、用戶管理等功能。

## 系統需求

- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL >= 5.7 或 SQLite
- NPM
- Docker (可選，用於容器化部署)

## 建置步驟

### 1. 環境設置

```bash
# 複製環境設定檔
cp .env.example .env

# 安裝 PHP 依賴
composer install

# 生成應用程式金鑰
php artisan key:generate
```

### 2. 資料庫設置

1. 在 `.env` 文件中設置資料庫連接資訊：
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_blog
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. 執行資料庫遷移：
```bash
php artisan migrate
```

3. 填充測試數據（可選）：
```bash
php artisan db:seed
```

### 3. 前端資源設置

```bash
# 安裝 NPM 依賴
npm install

# 編譯前端資源
npm run dev
```

### 4. 啟動開發伺服器

```bash
php artisan serve
```

### 5. Docker 部署 (可選)

如果您想使用 Docker 來部署應用程式，請按照以下步驟操作：

```bash
# 啟動 Docker 容器
docker-compose up -d

# 在容器中執行命令，例如遷移資料庫
docker-compose exec app php artisan migrate

# 運行測試
docker-compose exec app php artisan test
```

## 功能特點

- 用戶認證系統（註冊、登入、登出）
- 文章管理（發布、編輯、刪除）
- SEO 友好的 URL slug
- 分類和評論功能
- 響應式設計
- 繁體中文介面

## 專案文檔

為了幫助開發者和使用者更好地理解和使用本系統，我們提供了詳細的文檔：

- [技術概覽](docs/technical_overview.md) - 提供系統架構、功能和工具的全面介紹
- [開發者指南](docs/dev_guide.md) - 詳細的開發流程和最佳實踐
- [Docker 環境說明](docs/docker_environment.md) - Docker 配置和使用方法
- [Slug 功能說明](docs/slug_functionality.md) - SEO 友好 URL 的實現細節
- [多語言與本地化](docs/localization.md) - 多語言支持和繁體中文本地化說明

## 目錄結構說明

```
├── app/                 # 應用程式核心代碼
│   ├── Http/           # 控制器和中介層
│   ├── Models/         # 資料模型
│   └── Providers/      # 服務提供者
├── config/             # 設定檔
├── database/           # 資料庫遷移和種子
│   ├── factories/      # 模型工廠
│   ├── migrations/     # 資料庫遷移
│   └── seeders/        # 資料填充
├── docs/               # 專案文檔
├── public/             # 公開資源
├── resources/          # 視圖和前端資源
├── routes/             # 路由定義
└── tests/              # 測試檔案
```

## 開發指南

### 新增文章

1. 登入系統
2. 點擊導航欄的「文章」連結
3. 點擊「新增文章」按鈕
4. 填寫文章標題、內容、選擇分類
5. 標題會自動生成 SEO 友好的 URL slug
6. 點擊「發布」按鈕

### 編輯個人資料

1. 點擊右上角用戶名稱
2. 選擇「個人資料」
3. 修改所需資訊
4. 點擊「保存」按鈕

## 測試

系統包含完整的測試套件，確保功能正常運作：

```bash
# 運行所有測試
php artisan test

# 運行特定測試文件
php artisan test --filter=PostTest
```

## 貢獻指南

1. Fork 本專案
2. 創建新的功能分支
3. 提交更改
4. 發起 Pull Request

## 授權

本專案採用 MIT 授權條款。詳見 [LICENSE](LICENSE) 文件。 