#!/bin/bash

# 确保脚本在出错时退出
set -e

# 检查Laravel是否已安装
if [ ! -f "artisan" ]; then
    echo "正在创建新的Laravel项目..."
    
    # 创建临时目录
    mkdir -p /tmp/laravel-temp
    cd /tmp/laravel-temp
    
    # 在临时目录创建Laravel项目
    composer create-project --prefer-dist laravel/laravel .
    
    # 复制所有文件到工作目录，但排除已存在的Docker相关文件
    cd /var/www
    
    # 复制Laravel文件，但排除已存在的Docker相关文件
    cp -r /tmp/laravel-temp/app ./
    cp -r /tmp/laravel-temp/bootstrap ./
    cp -r /tmp/laravel-temp/config ./
    cp -r /tmp/laravel-temp/database ./
    cp -r /tmp/laravel-temp/public ./
    cp -r /tmp/laravel-temp/resources ./
    cp -r /tmp/laravel-temp/routes ./
    cp -r /tmp/laravel-temp/storage ./
    cp -r /tmp/laravel-temp/tests ./
    cp -r /tmp/laravel-temp/vendor ./
    cp -n /tmp/laravel-temp/artisan ./
    cp -n /tmp/laravel-temp/composer.json ./
    cp -n /tmp/laravel-temp/composer.lock ./
    cp -n /tmp/laravel-temp/package.json ./
    cp -n /tmp/laravel-temp/phpunit.xml ./
    cp -n /tmp/laravel-temp/vite.config.js ./
    
    # 复制.env文件(如果不存在)
    if [ ! -f ".env" ]; then
        cp /tmp/laravel-temp/.env.example .env
    fi
    
    # 清理临时目录
    rm -rf /tmp/laravel-temp
    
    # 生成应用密钥
    php artisan key:generate
else
    echo "Laravel已安装，跳过创建步骤"
fi

# 安装依赖
composer install

# 设置适当的权限
chmod -R 777 storage bootstrap/cache

# 运行迁移
php artisan migrate --force

echo "Laravel应用已准备就绪！" 