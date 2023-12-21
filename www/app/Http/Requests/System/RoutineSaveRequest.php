<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class RoutineSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active'         =>  'required|in:t,f',
            'name'              =>  'required|min:3',
            'slug'              =>  'required|min:3',
            'modules_id'        =>  'required|integer|exists:modules,id',
            'actions'           =>  'array',
            'actions.*.slug'    =>  'required|min:3',
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active'         =>  'ativo',
            'name'              =>  'nome',
            'modules_id'        =>  'módulo',
            'actions.*.slug'    =>  'slug'
        ];
    }
}
