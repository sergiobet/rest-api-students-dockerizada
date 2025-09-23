FROM php:8.2-fpm-alpine AS base

# Instalar dependencias del sistema y extensiones de PHP
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        mariadb-connector-c-dev \
        libzip-dev \
        oniguruma-dev \
        libpng-dev \
        jpeg-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libwebp-dev \
        icu-dev \
        libxml2-dev \
    && apk add --no-cache \
        libgd libzip \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring zip exif pcntl \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install opcache \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

# Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Crear usuario no-root
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

WORKDIR /var/www/html

# ----------------- Etapa de Compilación de Dependencias -----------------
FROM base AS vendor

# Copiar solo los archivos de dependencias para aprovechar el caché de Docker
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress --no-scripts

# ----------------- Etapa Final de Producción -----------------
FROM base AS production

# Copiar el código de la aplicación y las dependencias de las etapas anteriores
COPY --chown=www:www . .
COPY --from=vendor /var/www/html/vendor ./vendor

# Optimizar Laravel para producción
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan event:cache

# Ajustar permisos para que Laravel pueda escribir en los directorios necesarios
RUN chown -R www:www storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Cambiar al usuario no-root
USER www

# Copiar y dar permisos al entrypoint de producción
COPY docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
