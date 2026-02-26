<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
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
            // Otros campos específicos de la v1
        ]);
    }
}
