# Students REST API - Laravel 12

Una API REST moderna y robusta para la gestión de estudiantes, construida con **Laravel 12** y desplegada utilizando **FrankenPHP** en un entorno de contenedores.

[![Deploy to Render](https://img.shields.io/badge/Deploy-Render-430098?style=for-the-badge&logo=render&logoColor=white)](https://rest-api-students.onrender.com)
[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)

## Características Principales

- **Autenticación Segura**: Implementada con Laravel Sanctum (Tokens de acceso personal).
- **CRUD Completo de Estudiantes**: Gestión integral de registros de alumnos.
- **Arquitectura Versionada**: Estructura preparada para escalabilidad en `V1/` y futuras versiones.
- **Documentación Interactiva**: Integración total con **Swagger (OpenAPI 3.0)**.
- **Entorno de Producción**: Optimizado para Render con **FrankenPHP** y **PostgreSQL (Neon.tech)**.

## Stack Tecnológico

- **Backend**: Laravel 12 (PHP 8.2+)
- **Servidor Web**: FrankenPHP (Caddy-based)
- **Base de Datos**: PostgreSQL / MySQL
- **Documentación**: L5-Swagger (OpenAPI)
- **Contenerización**: Docker & Docker Compose

---

## Documentación de la API

La documentación interactiva se genera automáticamente a partir de anotaciones de OpenAPI. Puedes probar los endpoints directamente desde el navegador:

👉 **[Ver Swagger UI en Producción](https://rest-api-students.onrender.com/api/documentation)**

### Endpoints Principales (V1)

| Método | Ruta | Descripción |
| :--- | :--- | :--- |
| `POST` | `/api/login` | Obtener token de acceso |
| `GET` | `/api/v1/profile` | Ver perfil del usuario autenticado |
| `GET` | `/api/v1/students` | Listar todos los estudiantes |
| `POST` | `/api/v1/students` | Registrar un nuevo estudiante |
| `GET` | `/api/v1/students/{id}` | Ver detalles de un estudiante |

---

## Instalación Local

### Requisitos Previos
- Docker y Docker Compose
- Git

### Pasos
1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/sergiobet/rest-api-students-dockerizada.git
   cd rest-api-students-dockerizada
   ```

2. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   ```

3. **Levantar contenedores:**
   ```bash
   docker compose up -d
   ```

4. **Instalar dependencias y generar llave:**
   ```bash
   docker compose exec laravel-app composer install
   docker compose exec laravel-app php artisan key:generate
   ```

5. **Ejecutar migraciones:**
   ```bash
   docker compose exec laravel-app php artisan migrate
   ```

6. **Acceder a la app:**
   - API: `http://localhost:9000`
   - Swagger: `http://localhost:9000/api/documentation`

---

## Despliegue en Producción

El proyecto está configurado para ejecutarse en **Render** mediante el `Dockerfile` ubicado en `docker/prod/`.

**Configuraciones clave para el despliegue:**
- **Document Root**: Debe apuntar a la carpeta `/public`.
- **Variables de Entorno necesarias**:
  - `APP_ENV`: `production`
  - `APP_URL`: URL asignada por Render.
  - `DB_CONNECTION`: `pgsql` (para Neon.tech).
  - `L5_SWAGGER_CONST_HOST`: `https://tu-url-render.com/api`

---

## Licencia
Este proyecto es de código abierto bajo la licencia [MIT](https://opensource.org/licenses/MIT).
