<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

// Esto es para la documentación de la API con Swagger
#[OA\Tag(
    name: 'Profile',
    description: 'Perfil de usuario'
)]


class ProfileController extends Controller
{
    // Documentación del update usando autenticación
    #[OA\Get(
        path: '/v1/profile',
        summary: 'Ver perfil del usuario',
        description: 'Retorna los datos del usuario autenticado.',
        tags: ['Perfil'],
        security: [['bearerAuth' => []]], // Esto activa el candado del token
        responses: [
            new OA\Response(
                response: 200,
                description: 'Perfil del usuario',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'nombre', type: 'string', example: 'Juan Pérez'),
                        new OA\Property(property: 'email', type: 'string', example: 'juan@example.com')
                    ]
                )
            ),
            new OA\Response(
                response: 401, 
                description: 'No autorizado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]

    /**
     * Display the authenticated user's profile for API v1.
     */
    public function show(Request $request): JsonResponse
    {
        // Aquí puedes adaptar la respuesta para la v1
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'nombre' => $user->name, // Asumiendo que 'name' es el nombre completo en el modelo User
            'email' => $user->email,
        ]);
    }

    // Documentación del update usando autenticación
    #[OA\Put(
        path: '/v1/profile',
        summary: 'Actualizar perfil',
        description: 'Actualiza los datos del usuario autenticado.',
        tags: ['Perfil'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Juan Pérez Nuevo'),
                    new OA\Property(property: 'email', type: 'string', example: 'nuevo@example.com'),
                    new OA\Property(property: 'password', type: 'string', minLength: 8, example: 'nueva_clave123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Perfil actualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Perfil actualizado correctamente'),
                        new OA\Property(property: 'user', ref: '#/components/schemas/User')
                    ]
                )
            ),
            new OA\Response(
                response: 422, 
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
)]

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Si envían contraseña, la encriptamos antes de guardar
        $data = $validator->validated();
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
        
}
