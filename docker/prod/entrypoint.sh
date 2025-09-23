#!/bin/sh

# Salir inmediatamente si un comando falla.
set -e

# El rol del contenedor (app, scheduler, etc.). Por defecto es 'app'.
role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    # Esperar a que la base de datos esté lista (opcional pero recomendado)
    # En Render, el servicio de la base de datos puede tardar un momento en iniciar.
    # Este es un ejemplo simple, podrías necesitar un script más robusto como wait-for-it.sh
    # Por ahora, una simple espera puede ser suficiente.
    # sleep 10

    # Ejecutar migraciones y optimizaciones de Laravel
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Iniciar PHP-FPM
    exec php-fpm
fi
