#!/bin/sh

# Salir inmediatamente si un comando falla
set -e

# Instalar dependencias de Composer si no están presentes
if [ ! -f "vendor/autoload.php" ]; then
    composer install --optimize-autoloader
fi

# Ejecutar el comando principal del contenedor (php-fpm)
exec "$@"