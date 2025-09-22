<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

class studentController extends Controller
{
    public function index()
    {
        $students = Student::all();

        if (is_null($students)  || $students->isEmpty()) {
            $data = [
                'status' => 'error',
                'message' => 'No students found',
                'status' => 404
            ];
            return response()->json($data, 404);
        } else {
            $data = [
                'students' => $students,
                'status' => 200
            ];
            return response()->json($data, 200);
        }
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
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422
            ];
            return response()->json($data, 422);
        }

        $student = Student::create($request->all());

        if (is_null($student)) {
            $data = [
                'status' => 'error',
                'message' => 'Failed to create student',
                'status' => 500
            ];
            return response()->json($data, 500);
            
        } else {
            $data = [
                'status' => 'success',
                'message' => 'Student created successfully',
                'student' => $student,
                'status' => 201
            ];
            return response()->json($data, 201);
        }
        
    }

    public function show($id)
    {
        $student = Student::find($id);

        if (is_null($student)) {
            $data = [
                'status' => 'error',
                'message' => 'Student not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        } else {
            $data = [
                'student' => $student,
                'status' => 200
            ];
            return response()->json($data, 200);
        }
    }

    //Se utiliza softdelete para eliminar registros de manera lógica
    public function delete($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        } else {
           $student->delete();
           return response()->json(['message' => 'Student deleted successfully'], 200);
        }
        
    }

    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:student,email,' . $id,
            'phone' => 'sometimes|required|string|max:15|regex:/^[\d\s\-\+\(\)]+$/',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:10',
            'last_name' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $student->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Student updated successfully',
            'student' => $student
        ], 200);
    }
}