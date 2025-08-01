<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventarioLaboratorioRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        $rules = [
            'nombre_item' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cantidad_total' => 'required|integer|min:0',
            'cantidad_disponible' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'estado_item' => 'required|in:Disponible,Agotado,En mantenimiento,Dañado,Reservado',
        ];

        // Validación adicional: cantidad_disponible no puede ser mayor que cantidad_total
        $rules['cantidad_disponible'] = $rules['cantidad_disponible'] . '|lte:cantidad_total';

        return $rules;
    }

    public function attributes()
    {
        return [
            'nombre_item' => 'nombre del item',
            'descripcion' => 'descripción',
            'cantidad_total' => 'cantidad total',
            'cantidad_disponible' => 'cantidad disponible',
            'ubicacion' => 'ubicación',
            'estado_item' => 'estado del item',
        ];
    }

    public function messages()
    {
        return [
            'nombre_item.required' => 'El nombre del item es obligatorio.',
            'nombre_item.max' => 'El nombre del item no puede exceder 255 caracteres.',
            'cantidad_total.required' => 'La cantidad total es obligatoria.',
            'cantidad_total.integer' => 'La cantidad total debe ser un número entero.',
            'cantidad_total.min' => 'La cantidad total no puede ser negativa.',
            'cantidad_disponible.required' => 'La cantidad disponible es obligatoria.',
            'cantidad_disponible.integer' => 'La cantidad disponible debe ser un número entero.',
            'cantidad_disponible.min' => 'La cantidad disponible no puede ser negativa.',
            'cantidad_disponible.lte' => 'La cantidad disponible no puede ser mayor que la cantidad total.',
            'estado_item.required' => 'El estado del item es obligatorio.',
            'estado_item.in' => 'El estado seleccionado no es válido.',
            'ubicacion.max' => 'La ubicación no puede exceder 255 caracteres.',
        ];
    }

    protected function prepareForValidation()
    {
        // Limpiar espacios en blanco
        $this->merge([
            'nombre_item' => trim($this->nombre_item ?? ''),
            'descripcion' => trim($this->descripcion ?? ''),
            'ubicacion' => trim($this->ubicacion ?? ''),
        ]);
    }
}
