<?php

namespace App\Http\Requests\System\Messages;

use Illuminate\Foundation\Http\FormRequest;

class ContactSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active'     =>  'required|in:t,f',
            'name'          =>  'required|min:3',
            'email'         =>  'nullable|email'
        ];
    }

    public function attributes(): array
    {
        return [
            'name'          =>  'nome',
            'email'         =>  'email'
        ];
    }
}
