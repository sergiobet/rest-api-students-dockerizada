<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    /**
     * Configuramos un usuario autenticado para todas las pruebas de este archivo
     * ya que las rutas de estudiantes están protegidas por Sanctum.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Creamos un usuario y lo autenticamos
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Prueba que se puede obtener la lista de estudiantes.
     */
    public function test_can_list_students(): void
    {
        // Preparar
        Student::factory()->count(3)->create();

        // Actuar
        $response = $this->getJson('/api/v1/students');

        // Verificar
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Prueba la creación de un estudiante.
     */
    public function test_can_create_student(): void
    {
        $data = [
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan.perez@example.com',
            'phone' => '123456789',
            'age' => 20,
            'gender' => 'Male',
            'address' => 'Calle Falsa 123'
        ];

        $response = $this->postJson('/api/v1/students', $data);

        $response->assertStatus(201)
            ->assertJsonPath('student.correo', $data['email']);

        $this->assertDatabaseHas('students', [
            'email' => $data['email'],
            'deleted_at' => null
        ]);
    }

    /**
     * Prueba que la validación funciona al crear un estudiante.
     */
    public function test_validation_fails_when_creating_student_with_duplicate_email(): void
    {
        // Creamos un estudiante previo
        Student::factory()->create(['email' => 'duplicado@example.com']);

        $data = [
            'name' => 'Nuevo',
            'email' => 'duplicado@example.com',
            'phone' => '987654321'
        ];

        $response = $this->postJson('/api/v1/students', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Prueba la obtención de un estudiante individual.
     */
    public function test_can_show_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonPath('student.id', $student->id);
    }

    /**
     * Prueba la actualización de un estudiante.
     */
    public function test_can_update_student(): void
    {
        $student = Student::factory()->create(['name' => 'Original']);

        $response = $this->putJson("/api/v1/students/{$student->id}", [
            'name' => 'Actualizado'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Actualizado'
        ]);
    }

    /**
     * Prueba el borrado lógico (Soft Delete).
     */
    public function test_can_soft_delete_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->deleteJson("/api/v1/students/{$student->id}");

        $response->assertStatus(200);

        // Verificamos que el registro sigue en la DB pero con deleted_at
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }
}
