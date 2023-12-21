<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class ModuleSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' =>  'in:t,f',
            'name'      =>  'required|min:3',
            'slug'      =>  "unique:modules,slug" . ($this->id ? ",{$this->id}" : ''),
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active'     =>  'ativo',
            'name'          =>  'nome',
        ];
    }
}
