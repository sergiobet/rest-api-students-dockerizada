FROM php:8.2-fpm-alpine

# Install build dependencies and PHP extensions in a single layer
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        mariadb-connector-c-dev \
        imagemagick-dev \
        libzip-dev \
        oniguruma-dev \
        libpng-dev \
        jpeg-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libwebp-dev \
        libxpm-dev \
        libvpx-dev \
        icu-dev \
        bzip2-dev \
        libxml2-dev \
        libxslt-dev \
        curl-dev \
        openssl-dev \
        gmp-dev \
        yaml-dev \
        sqlite-dev \
        readline-dev \
    && apk add --no-cache \
        git curl nano vim net-tools iputils libgd \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring zip exif pcntl \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install opcache \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

# RUN pecl install redis \
#     && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY /docker/dev/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]