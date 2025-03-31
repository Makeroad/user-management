FROM php:8.2-fpm

# パッケージインストール
RUN apt-get update && apt-get install -y \
    sqlite3 libsqlite3-dev zip unzip curl git \
    && docker-php-ext-install pdo pdo_sqlite

# Node.js インストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs
    
# Composer インストール
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www

CMD ["php-fpm"]