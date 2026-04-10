<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// /home/ragnarok/rest-api-students/routes/api.php

Route::post('/login', [AuthController::class, 'login'])->name('login');

// --- Rutas de API Versionadas ---
// Carga las rutas de la versión 1
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/v1.php';
});

// Carga las rutas de la versión 2
// Route::prefix('v2')->middleware('auth:sanctum')->group(function () {
//     require __DIR__ . '/api/v2.php';
// });
