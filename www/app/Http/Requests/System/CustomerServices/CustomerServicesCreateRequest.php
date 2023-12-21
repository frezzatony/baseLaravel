<?php

namespace App\Http\Requests\System\CustomerServices;

use Illuminate\Foundation\Http\FormRequest;

class CustomerServicesCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'queue'         =>  'required|exists:queues,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'queue'         =>  'fila de atendimento',
        ];
    }
}
