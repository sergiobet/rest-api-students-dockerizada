<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use OpenApi\Attributes as OA;

// Esto es para la documentación de la API con Swagger
#[OA\Tag(
    name: 'Auth',
    description: 'Autenticación de usuarios'
)]

class AuthController extends Controller
{
    // Documentación del login para obtener el token de acceso, se utilizo Sanctum para la autenticación
    #[OA\Post(
    path: '/login',
    summary: 'Iniciar sesión',
    description: 'Autentica al usuario y devuelve un token de acceso.',
    tags: ['Autenticación'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123')
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Login exitoso',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'token', type: 'string', example: '1|abc...'),
                    new OA\Property(property: 'message', type: 'string', example: 'Login exitoso'),
                    new OA\Property(property: 'user', ref: '#/components/schemas/User')
                ]
            )
        ),
        //Se reutiliza el esquema global de ErrorResponse definido en Controller.php
        new OA\Response(
        response: 401, 
        description: 'Credenciales inválidas',
        content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
)
    ]
)]
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // Verificamos si existe el usuario y la contraseña es correcta
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Si es correcto, generamos el token
        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }
}

