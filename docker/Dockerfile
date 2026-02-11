FROM php:8.5-rc-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    libxml2-dev \
    autoconf \
    gcc \
    g++ \
    make \
    linux-headers \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    intl \
    pdo_pgsql \
    mbstring \
    soap \
    xml \
    && docker-php-ext-enable \
    pdo_mysql \
    zip \
    intl \
    pdo_pgsql \
    mbstring \
    soap \
    xml

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/html

CMD ["php-fpm"]
