<?php

namespace App\Http\Requests\System\Person;

use Illuminate\Foundation\Http\FormRequest;

class NaturalSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validation = [
            'is_active'         =>  'in:t,f',
            'name'              =>  'required|min:3',
            'cpf_cnpj'          =>  'required|cpf|unique:persons,cpf_cnpj' . ($this->id ? ",{$this->id}" : ''),
            'contact.*.contact' =>  'required',
        ];

        return $validation;
    }

    public function attributes(): array
    {
        return [
            'is_active'         =>  'ativa',
            'name'              =>  'nome',
            'cpf_cnpj'          =>  'CPF',
            'contact.*.type'    =>  'tipo de contato',
            'contact.*.contact' =>  'contato',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf_cnpj' => preg_replace('/[^0-9]/', '', $this->cpf_cnpj),
        ]);
    }
}
