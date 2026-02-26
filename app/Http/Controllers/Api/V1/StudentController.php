<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\StudentResource;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        // StudentResource::collection se encarga de transformar una colección de modelos.
        // Laravel devolverá un array JSON vacío si no hay estudiantes, lo cual es correcto.
        return StudentResource::collection($students)->additional(['status' => 200]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:15|regex:/^[\d\s\-\+\(\)]+$/',
            'address' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:100',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:10'
        ]);

        if ($validator->fails()) {
            $data = [
                'status_code' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
            return response()->json($data, 422);
        }

        $student = Student::create($request->all());

        // Devolvemos el recurso para mantener la consistencia en las respuestas.
        // El código de estado 201 (Created) es más apropiado aquí.
        return (new StudentResource($student))
            ->additional(['message' => 'Student created successfully', 'status' => 201])
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        // Usamos findOrFail para que Laravel lance automáticamente un 404 si no lo encuentra.
        // El Exception Handler que configuramos se encargará de la respuesta JSON.
        $student = Student::findOrFail($id);
        // Pasamos el modelo encontrado al resource para que lo transforme.
        return (new StudentResource($student))->additional(['status' => 200]);
    }

    // apiResource usa 'destroy' para la ruta DELETE.
    // Se utiliza softdelete para eliminar registros de manera lógica
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully', 'status' => 200], 200);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            // Corregido: 'student' a 'students' en la regla unique
            'email' => 'sometimes|required|email|unique:students,email,' . $id,
            'phone' => 'sometimes|required|string|max:15|regex:/^[\d\s\-\+\(\)]+$/',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:10',
            'last_name' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $student->update($request->all());

        return (new StudentResource($student))->additional([
            'status' => 200,
            'message' => 'Student updated successfully',
        ]);
    }
}