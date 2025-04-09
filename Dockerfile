FROM php:8.2-fpm

# 设置工作目录
WORKDIR /var/www

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# 安装Node.js和npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 清理apt缓存
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 安装PHP扩展
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 获取最新版本的Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 创建系统用户以运行Composer和Artisan命令
RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel
RUN mkdir -p /home/laravel/.composer && \
    chown -R laravel:laravel /home/laravel

# 复制应用代码
COPY . /var/www

# 设置正确的权限
RUN chown -R laravel:laravel /var/www

# 切换到非root用户
USER laravel

# 暴露端口9000并启动php-fpm服务
EXPOSE 9000
CMD ["php-fpm"] 