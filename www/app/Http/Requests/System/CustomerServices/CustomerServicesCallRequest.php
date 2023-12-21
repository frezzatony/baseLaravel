<?php

namespace App\Http\Requests\System\CustomerServices;

use Illuminate\Foundation\Http\FormRequest;

class CustomerServicesCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'queue'         =>  'required',
            'service_point' =>  'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'queue'         =>  'fila de atendimento',
            'service_point' =>  'ponto de atendimento',
        ];
    }
}
