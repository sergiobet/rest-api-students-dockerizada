<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * El trait RefreshDatabase se encarga de vaciar la base de datos (en memoria) 
     * antes de cada prueba para que una prueba no afecte a la siguiente.
     */
    use RefreshDatabase;

    /**
     * Prueba que un usuario puede iniciar sesión con credenciales válidas.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // 1. Preparación (Arrange): Creamos un usuario en la DB de pruebas
        $password = 'password123';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        // 2. Acción (Act): Hacemos una petición POST al endpoint de login
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // 3. Verificación (Assert): Comprobamos que la respuesta sea correcta
        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /**
     * Prueba que un usuario no puede iniciar sesión con una contraseña incorrecta.
     */
    public function test_user_cannot_login_with_incorrect_password(): void
    {
        // 1. Preparación
        $user = User::factory()->create([
            'password' => 'password_valido',
        ]);

        // 2. Acción
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'contraseña_incorrecta',
        ]);

        // 3. Verificación
        $response->assertStatus(401)
            ->assertJson(['message' => 'Credenciales inválidas']);
    }
}
