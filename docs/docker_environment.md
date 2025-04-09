# Docker 開發環境

本文檔詳細說明如何在 Docker 環境中開發和部署 Laravel 部落格系統。

## 1. 環境組件

本項目使用 Docker 和 Docker Compose 來構建一個隔離且一致的開發環境，包含以下容器：

- **app**: PHP 8.1 應用容器，運行 Laravel 應用程式
- **mysql**: MySQL 5.7 數據庫容器
- **nginx**: Nginx Web 服務器容器（可選）
- **redis**: Redis 緩存服務容器（可選）

## 2. 目錄結構

與 Docker 相關的文件包括：

```
├── Dockerfile          # 應用容器的構建指令
├── docker-compose.yml  # 容器編排配置
├── docker/             # Docker 相關配置文件
│   ├── nginx/          # Nginx 配置
│   ├── php/            # PHP 配置
│   └── mysql/          # MySQL 配置
└── start.sh            # 容器啟動腳本
```

## 3. 快速開始

### 3.1 環境要求

- Docker Engine (20.10+)
- Docker Compose (2.0+)
- Git

### 3.2 設置步驟

1. 克隆項目並進入目錄：

```bash
git clone <repository-url>
cd laravel-blog
```

2. 創建環境配置文件：

```bash
cp .env.example .env
```

3. 更新 `.env` 中的數據庫設置為 Docker 配置：

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_blog
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. 啟動 Docker 容器：

```bash
docker-compose up -d
```

5. 進入應用容器並安裝依賴：

```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
```

6. 遷移數據庫並填充測試數據：

```bash
docker-compose exec app php artisan migrate --seed
```

7. 編譯前端資源：

```bash
docker-compose exec app npm install
docker-compose exec app npm run dev
```

8. 訪問應用：http://localhost:8000

## 4. 常用 Docker 命令

### 4.1 容器管理

```bash
# 啟動所有容器
docker-compose up -d

# 停止所有容器
docker-compose down

# 重啟特定容器
docker-compose restart app

# 查看容器日誌
docker-compose logs -f app

# 列出運行中的容器
docker-compose ps
```

### 4.2 執行命令

```bash
# 在應用容器中執行 Artisan 命令
docker-compose exec app php artisan <command>

# 在應用容器中執行 Composer 命令
docker-compose exec app composer <command>

# 在應用容器中執行 NPM 命令
docker-compose exec app npm <command>

# 在應用容器中運行測試
docker-compose exec app php artisan test

# 在應用容器中開啟 Shell
docker-compose exec app bash
```

## 5. 配置詳解

### 5.1 docker-compose.yml

此文件定義了應用程式所需的所有服務容器：

```yaml
version: '3'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: laravel_app
    restart: unless-stopped
    volumes:
      - .:/var/www
    networks:
      - laravel_net
    depends_on:
      - mysql

  mysql:
    image: mysql:5.7
    container_name: laravel_mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
      SERVICE_TAGS: dev
      SERVICE_NAME: mysql
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - laravel_net

  # 可選服務：Nginx
  nginx:
    image: nginx:alpine
    container_name: laravel_nginx
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - .:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - laravel_net
    depends_on:
      - app

  # 可選服務：Redis
  redis:
    image: redis:alpine
    container_name: laravel_redis
    restart: unless-stopped
    networks:
      - laravel_net

networks:
  laravel_net:
    driver: bridge

volumes:
  mysql_data:
    driver: local
```

### 5.2 Dockerfile

此文件定義了應用容器的構建流程：

```dockerfile
FROM php:8.1-fpm

# 安裝系統依賴
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# 清理緩存
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 安裝 PHP 擴展
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 獲取最新版 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 設置工作目錄
WORKDIR /var/www

# 創建系統用戶
RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www

# 複製應用程式代碼
COPY . /var/www

# 設置適當的權限
RUN chown -R www:www /var/www

# 切換用戶
USER www

# 暴露端口
EXPOSE 9000

# 啟動 PHP-FPM
CMD ["php-fpm"]
```

### 5.3 start.sh

應用程式啟動腳本，執行一系列初始化操作：

```bash
#!/bin/bash

# 等待 MySQL 啟動
echo "Waiting for MySQL to start..."
while ! nc -z mysql 3306; do
  sleep 1
done
echo "MySQL started"

# 安裝 Composer 依賴
echo "Installing Composer dependencies..."
composer install --no-interaction --optimize-autoloader

# 設置適當的權限
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# 遷移數據庫
echo "Migrating database..."
php artisan migrate --force

# 清除緩存
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 啟動 PHP-FPM
echo "Starting PHP-FPM..."
php-fpm
```

## 6. 常見問題與解決方案

### 6.1 容器連接問題

**問題**：容器之間無法通信

**解決方案**：
- 確保所有容器都在同一網絡下
- 檢查容器名稱是否正確
- 使用 `docker-compose ps` 檢查容器是否正常運行

### 6.2 權限問題

**問題**：文件權限錯誤

**解決方案**：
- 確保容器內外的用戶 ID 一致
- 運行命令設置適當的權限：
  ```bash
  docker-compose exec app chown -R www:www /var/www/storage
  docker-compose exec app chmod -R 775 /var/www/storage
  ```

### 6.3 數據庫連接失敗

**問題**：應用無法連接到數據庫

**解決方案**：
- 檢查 `.env` 中的數據庫設置是否正確
- 確保 MySQL 容器正在運行
- 嘗試使用 MySQL 客戶端直接連接檢查：
  ```bash
  docker-compose exec mysql mysql -u root -p
  ```

### 6.4 緩存相關問題

**問題**：配置或路由更改不生效

**解決方案**：
- 清除所有緩存：
  ```bash
  docker-compose exec app php artisan cache:clear
  docker-compose exec app php artisan config:clear
  docker-compose exec app php artisan route:clear
  docker-compose exec app php artisan view:clear
  ```

## 7. 生產環境部署

### 7.1 生產環境優化

在部署到生產環境之前，應該進行以下優化：

```bash
# 優化 Composer 自動加載
docker-compose exec app composer install --optimize-autoloader --no-dev

# 優化配置加載
docker-compose exec app php artisan config:cache

# 優化路由加載
docker-compose exec app php artisan route:cache

# 編譯前端資源
docker-compose exec app npm run build
```

### 7.2 生產環境配置調整

- 在 `.env` 文件中設置 `APP_ENV=production` 和 `APP_DEBUG=false`
- 確保所有敏感信息（密碼、API 密鑰等）都是安全的
- 使用適當的 PHP-FPM 和 Nginx 配置優化性能
- 考慮使用 Redis 進行緩存和隊列處理

### 7.3 監控和日誌

- 設置適當的日誌輪轉策略
- 考慮使用監控工具如 Prometheus 或 New Relic
- 設置自動化的健康檢查和警報

## 8. 高級使用

### 8.1 擴展 Dockerfile

可以根據需要擴展 Dockerfile 安裝額外的 PHP 擴展或工具：

```dockerfile
# 安裝 Redis 擴展
RUN pecl install redis && docker-php-ext-enable redis

# 安裝 Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
```

### 8.2 使用 Docker Compose 覆蓋文件

為不同環境創建不同的 Docker Compose 覆蓋文件：

```
docker-compose.yml          # 基本配置
docker-compose.override.yml # 本地開發覆蓋
docker-compose.prod.yml     # 生產環境覆蓋
```

使用特定配置：

```bash
# 使用生產配置
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 8.3 數據持久化

確保關鍵數據使用 Docker 卷持久化：

```yaml
volumes:
  mysql_data:
    driver: local
  redis_data:
    driver: local
  app_storage:
    driver: local
```

## 9. 資源與進一步閱讀

- [Docker 官方文檔](https://docs.docker.com/)
- [Docker Compose 文檔](https://docs.docker.com/compose/)
- [Laravel 官方部署指南](https://laravel.com/docs/deployment)
- [數字海洋的 Laravel 部署教程](https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-laravel-with-lemp-on-ubuntu-18-04) 