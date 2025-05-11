FROM php:8.3-fpm

ARG USER_ID
ARG GROUP_ID
ARG APP_ENV

# Устанавливаем необходимые зависимости
RUN apt-get update && apt-get install -y \
    bash-completion \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    unzip \
    make \
    curl \
    zip \
    git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Расширения PHP
RUN docker-php-ext-install zip pdo pdo_mysql pdo_pgsql

# Алиасы и автодополнение
RUN echo "alias ll='ls -alF'" >> /etc/bash.bashrc
RUN echo 'source /etc/bash_completion' >> /etc/bash.bashrc

# Создаем группу и пользователя
RUN groupadd -g ${GROUP_ID} appgroup && \
    useradd -u ${USER_ID} -g appgroup -m appuser

# Устанавливаем права на директории
RUN chown -R appuser:appgroup /var/www/html

# Логи Xdebug (только для dev)
RUN if [ "${APP_ENV}" = "dev" ]; then \
    mkdir -p /var/log/xdebug && \
    chown -R appuser:appgroup /var/log/xdebug && \
    chmod -R 766 /var/log/xdebug; \
    fi

# Конфиги PHP
COPY docker/php/conf.d/php.ini /usr/local/etc/php/conf.d/
COPY docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Устанавливаем Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Xdebug (только для dev)
RUN if [ "${APP_ENV}" = "dev" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    pecl clear-cache \
    rm -rf /tmp/pear; \
    fi

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Устанавливаем пользователя в окружении
USER appuser

# Копируем все файлы
COPY . .

# Устанавливаем зависимости
RUN if [ "${APP_ENV}" = "prod" ]; then \
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist; \
    else \
    composer install --no-interaction --prefer-dist; \
    fi

# Указываем пользователя для выполнения команд
USER appuser:appgroup
