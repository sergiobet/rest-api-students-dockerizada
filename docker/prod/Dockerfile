#Imagen base moderna y ligera
FROM dunglas/frankenphp:php8.2 AS base

#Instalamos las herramientas necesarias para que Laravel hable con PostgreSQL
RUN install-php-extensions \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    gd \
    opcache

# Instalamos libcap2-bin para poder quitarle las "capacidades" a FrankenPHP.
# Esto es necesario para que Render nos permita ejecutar el binario.
RUN apt-get update && apt-get install -y libcap2-bin \
    && setcap -r /usr/local/bin/frankenphp \
    && apt-get purge -y libcap2-bin \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

#Configuramos PHP para modo producción
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

#Instalamos Composer y bajamos las librerías
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

#Copiamos el código al contenedor y damos permisos de escritura a Laravel
COPY . .
RUN chown -R www-data:www-data storage bootstrap/cache

#Variables de entorno específicas para FrankenPHP
ENV SERVER_NAME=:10000
ENV FRANKENPHP_CONFIG="import /usr/local/etc/frankenphp/Caddyfile"

#Script de inicio
COPY docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

#Comando para iniciar el servidor de FrankenPHP
CMD ["frankenphp", "php-server"]

