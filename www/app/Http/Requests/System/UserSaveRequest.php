<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class UserSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $data = [
            'is_active' =>  'in:t,f',
            'name'      =>  'required|min:3',
            'login'     =>  "cpf|unique:users,login" . ($this->id ? ",{$this->id}" : ''),
            'email'     =>  'required|email',
        ];

        if (empty($this->id)) {
            $data['password'] = 'required|min:6|confirmed';
        }
        if (!empty($this->id)) {
            $data['password'] = 'nullable|min:6|confirmed';
        }
        return $data;
    }

    public function attributes(): array
    {
        return [
            'is_active'     =>  'ativo',
            'name'          =>  'nome',
            'login'         =>  'CPF',
            'email'         =>  'email',
            'password'      =>  'senha',
            'profiles'      =>  'required|array',
            'profiles.*'    =>  'integer'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'login' => preg_replace('/[^0-9]/', '', $this->login),
        ]);
    }
}
