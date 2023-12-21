<?php

namespace App\Http\Requests\System\Messages;

use Illuminate\Foundation\Http\FormRequest;

class CategorySaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active'     =>  'in:t,f',
            'description'   =>  'required|min:3',
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active'     =>  'ativa',
            'description'   =>  'descrição',
        ];
    }
}
