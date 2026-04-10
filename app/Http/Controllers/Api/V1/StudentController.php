<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\StudentResource;
use OpenApi\Attributes as OA;

class StudentController extends Controller
{
    #[OA\Get(
        path: '/v1/students',
        summary: 'Listar todos los estudiantes',
        description: 'Retorna una lista paginada de estudiantes con soporte para filtros y ordenación.',
        tags: ['Estudiantes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', description: 'Filtrar por nombre', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'email', in: 'query', description: 'Filtrar por correo electrónico', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', in: 'query', description: 'Campo de ordenación', required: false, schema: new OA\Schema(type: 'string', enum: ['id', 'name', 'created_at'])),
            new OA\Parameter(name: 'sort_order', in: 'query', description: 'Orden (asc/desc)', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Elementos por página', required: false, schema: new OA\Schema(type: 'integer', default: 10))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Éxito',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Student')),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))
        ]
    )]

    public function index(Request $request)
    {
        $query = Student::query();

        // Filtrado por nombre
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%')
                ->orWhere('last_name', 'like', '%' . $request->name . '%');
        }

        // Filtrado por email
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Ordenación básica
        $allowedSortFields = ['id', 'name', 'last_name', 'age', 'gender', 'email', 'phone', 'address', 'created_at'];
        $sortField = $request->get('sort_by', 'created_at');
        
        // Si el campo enviado no está en los permitidos, usamos por defecto 'created_at'
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        $allowedSortOrders = ['asc', 'desc'];
        $sortOrder = strtolower($request->get('sort_order', 'desc'));
        
        // Si el orden no es asc o desc, usamos 'desc' por defecto
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortField, $sortOrder);

        // Paginación (por defecto 10 elementos)
        $perPage = $request->get('per_page', 10);
        $students = $query->paginate($perPage);

        // StudentResource::collection se encarga de transformar una colección de modelos.
        // Laravel devolverá metadatos de paginación automáticamente.
        return StudentResource::collection($students)->additional(['status' => 200]);
    }

    #[OA\Post(
        path: '/v1/students',
        summary: 'Crear un nuevo estudiante',
        tags: ['Estudiantes'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['name', 'email', 'phone'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Juan'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan.perez@example.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '+56912345678'),
                    new OA\Property(property: 'last_name', type: 'string', example: 'Pérez'),
                    new OA\Property(property: 'age', type: 'integer', example: 21)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: 'Creado correctamente', 
                content: new OA\JsonContent(ref: '#/components/schemas/Student')
            ),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))

        ]
    )]


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

        $student = Student::create($validator->validated());

        // Devolvemos el recurso para mantener la consistencia en las respuestas.
        // El código de estado 201 (Created) es más apropiado aquí.
        return (new StudentResource($student))
            ->additional(['message' => 'Student created successfully', 'status' => 201])
            ->response()
            ->setStatusCode(201);
    }

    // Documentación para obtener un estudiante por id
    #[OA\Get(
        path: '/v1/students/{id}',
        summary: 'Mostrar un estudiante',
        description: 'Retorna los detalles de un estudiante específico.',
        tags: ['Estudiantes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID del estudiante',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Estudiante encontrado', content: new OA\JsonContent(ref: '#/components/schemas/Student')),
            new OA\Response(response: 404, description: 'Estudiante no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))
        ]
    )]
    
    public function show($id)
    {
        // Usamos findOrFail para que Laravel lance automáticamente un 404 si no lo encuentra.
        // El Exception Handler que configuramos se encargará de la respuesta JSON.
        $student = Student::findOrFail($id);
        // Pasamos el modelo encontrado al resource para que lo transforme.
        return (new StudentResource($student))->additional(['status' => 200]);
    }

    //Documentación de swagger para eliminar estudiante por ID
    #[OA\Delete(
        path: '/v1/students/{id}',
        summary: 'Eliminar estudiante',
        tags: ['Estudiantes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Eliminado correctamente'),
            new OA\Response(response: 404, description: 'No encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))
        ]
    )]  

    // apiResource usa 'destroy' para la ruta DELETE.
    // Se utiliza softdelete para eliminar registros de manera lógica
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully', 'status' => 200], 200);
    }

    //Documentación para actualizar estudiante por ID
    #[OA\Put(
        path: '/v1/students/{id}',
        summary: 'Actualizar estudiante',
        tags: ['Estudiantes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/Student')// Se reutiliza el schema de Student
        ),
        responses: [
            new OA\Response(response: 200, description: 'Actualizado correctamente'),
            new OA\Response(response: 404, description: 'No encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))
        ]
    )]
    
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