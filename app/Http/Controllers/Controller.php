<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;

//Documentación de información general de la API
#[OA\Info(
    title: 'API de Estudiantes',
    version: '1.0.0',
    description: 'Documentación de la API de gestión de estudiantes con autenticación Sanctum'
)]

//Ruta de la documentación de la API en desarrollo
#[OA\Server(
    url: 'http://localhost:9000/api', 
    description: 'Servidor de Desarrollo'
) ]

//Ruta de la documentación de la API en producción
#[OA\Server(
    url: 'https://tu-app.onrender.com/api', 
    description: 'Servidor de Producción'
)]

//Documentación de seguridad para la API
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth', 
    type: 'http', 
    scheme: 'bearer', 
    bearerFormat: 'JWT', 
    description: 'Ingrese el token en formato: Bearer <token>'
)]

//Documentación de error genérico para la API
#[OA\Schema(
    schema: 'ErrorResponse',
    title: 'Error Response',
    description: 'Estructura genérica para respuestas de error',
    properties: [
        new OA\Property(property: 'status', type: 'integer', example: 404),
        new OA\Property(property: 'message', type: 'string', example: 'El recurso solicitado no fue encontrado.')
    ]
)]

abstract class Controller
{
    //
}
