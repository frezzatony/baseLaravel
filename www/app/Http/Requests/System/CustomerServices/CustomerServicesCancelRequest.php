<?php

namespace App\Http\Requests\System\CustomerServices;

use Illuminate\Foundation\Http\FormRequest;

class CustomerServicesCancelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'queue'         =>  'required',
            'justification' =>  'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'queue'         =>  'fila de atendimento',
            'justification' =>  'justificativa',
        ];
    }
}
