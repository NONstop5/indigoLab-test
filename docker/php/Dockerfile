FROM php:8.3-fpm

# Устанавливаем необходимые зависимости
RUN apt-get update && apt-get install -y \
    git \
    libmcrypt-dev \
    libzip-dev \
    libpq-dev \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP
RUN docker-php-ext-install zip pdo pdo_pgsql

# Настройка PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/

# Устанавливаем Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Устанавливаем Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Копируем конфигурацию Xdebug
#COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем весь исходный код
COPY . /var/www/html

# Устанавливаем зависимости Composer
RUN composer install

# Указываем пользователя для выполнения команд
USER www-data

# Права для веб-сервера
#RUN chown -R www-data:www-data /var/www/html/var
