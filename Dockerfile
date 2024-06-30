FROM php:8.1-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Очистка кеша apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копирование файлов composer
COPY composer.json composer.lock ./

# Установка зависимостей
RUN composer install --no-scripts --no-autoloader

# Копирование исходного кода
COPY . .

# Генерация автозагрузчика
RUN composer dump-autoload --optimize

# Изменение прав доступа
RUN chown -R www-data:www-data /var/www

# Экспозиция порта 9000 и запуск php-fpm
EXPOSE 9000
CMD ["php-fpm"]
