<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'student';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Se define la estructura exacta de la respuesta para la v1
        return [
            'id' => $this->id,
            'nombre_completo' => $this->name . ' ' . $this->last_name,
            'correo' => $this->email,
            'telefono' => $this->phone,
            'registrado_en' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
