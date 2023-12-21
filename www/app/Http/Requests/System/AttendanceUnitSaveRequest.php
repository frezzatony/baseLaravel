<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUnitSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active'             =>  'required|in:t,f',
            'name'                  =>  'required|min:3',
            'slug'                  =>  "required|unique:attendance_units,slug" . ($this->id ? ",{$this->id}" : ''),
            'address_cep'           =>  'required|formato_cep',
            'address_state'         =>  'required',
            'address_city'          =>  'required|min:2',
            'address_neighborhood'  =>  'required',
            'address_street'        =>  'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  =>  'nome',
            'address_cep'           =>  'CEP',
            'address_state'         =>  'estado',
            'address_city'          =>  'cidade',
            'address_neighborhood'  =>  'bairro/localidade',
            'address_street'        =>  'logradouro',
        ];
    }
}
