<?php

use App\Http\Controllers\studentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Models\User;

// /home/ragnarok/rest-api-students/routes/api.php

Route::post('/login', function (Request $request) { // Correcto, usa Route::post
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    return response()->json([
        'token' => $user->createToken('api-token')->plainTextToken,
    ]);
})->name('login');


// --- Rutas de API Versionadas ---
// Carga las rutas de la versión 1
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/v1.php';
});

// Carga las rutas de la versión 2
// Route::prefix('v2')->middleware('auth:sanctum')->group(function () {
//     require __DIR__ . '/api/v2.php';
// });
