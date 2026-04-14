#!/bin/sh

#Salir inmediatamente si algún comando falla
set -e

#Ejecutar las migraciones automáticamente
echo "Ejecutando migraciones..."
php artisan migrate --force -vvv

#Optimizar el rendimiento de la API
echo "Optimizando caché..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

#Entregar el control al comando final (FrankenPHP)
echo "Iniciando FrankenPHP..."
exec "$@"
