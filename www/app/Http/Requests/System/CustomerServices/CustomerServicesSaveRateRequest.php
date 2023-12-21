<?php

namespace App\Http\Requests\System\CustomerServices;

use App\Helpers\Crud\System\CustomerService\Forms\DefaultHelper;
use App\Helpers\CrudHelper;
use Illuminate\Foundation\Http\FormRequest;

class CustomerServicesSaveRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            [
                'queue_id'          =>  'required|exists:queues,id',
                'person_cpf'        =>  'nullable|cpf|unique:persons,cpf_cnpj' . ($this->person_id ? ",{$this->person_id}" : ''),
                'person_name'       =>  !empty($this->person_cpf) ? 'required|min:3' : '',
                'contact.*.contact' =>  'required',
            ],
            CrudHelper::getFormDataValidation(DefaultHelper::inputs())['rules']
        );
    }

    public function attributes(): array
    {
        return array_merge(
            [
                'queue_id'          =>  'fila de atendimento',
                'person_cpf'        =>  'CPF',
                'person_name'       =>  'nome',
                'contact.*.contact' =>  'contato',
            ],
            CrudHelper::getFormDataValidation(DefaultHelper::inputs())['attributes']
        );
    }
}
