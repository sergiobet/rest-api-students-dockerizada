#!/bin/sh

#Salir inmediatamente si algún comando falla
set -e

#Ejecutar las migraciones automáticamente
echo "Ejecutando migraciones..."
php artisan migrate --force -vvv

#Generar documentación de Swagger antes de optimizar para evitar conflictos por las políticas de seguridad de Render
echo "Generando documentación de Swagger..."
php artisan l5-swagger:generate

#Optimizar el rendimiento de la API
echo "Optimizando caché..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

#Entregar el control al comando final (FrankenPHP)
echo "Iniciando FrankenPHP..."
exec "$@"
