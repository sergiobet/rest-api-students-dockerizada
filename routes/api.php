<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\studentController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/students/{id}', [studentController::class, 'show']);

Route::get('/students', [studentController::class, 'index']);

Route::post('/students', [studentController::class, 'store']);

Route::delete('/students/{id}', [studentController::class, 'delete']);

Route::put('/students/{id}', [studentController::class, 'update']);





