<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

// Esto es para la documentación de la API con Swagger
#[OA\Schema(
    schema: 'Student',
    title: 'Student',
    properties: [
        new OA\Property(property: 'id', type: 'integer', readOnly: true, example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Juan'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan.perez@example.com'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Pérez'),
        new OA\Property(property: 'phone', type: 'string', example: '+56912345678'),
        new OA\Property(property: 'age', type: 'integer', example: 20),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', readOnly: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', readOnly: true)
    ]
)]

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'name',
        'last_name',
        'age',
        'gender',
        'address',
        'email',
        'phone',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    use HasFactory, SoftDeletes;
}

?>