<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\ProfileController;

// Rutas para el perfil de usuario (v1)
Route::get('/profile', [ProfileController::class, 'show']);

// Rutas de Estudiantes (v1)
Route::apiResource('students', StudentController::class);
