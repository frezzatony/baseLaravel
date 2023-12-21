<?php

namespace App\Http\Requests\System\Queue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FirstComeManualSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request): array
    {
        return [
            'is_active'                 =>  'in:t,f',
            'description'               =>  'required|min:3',
            'point_name'                =>  'required|min:3',
            'attendance_units_id'       =>  'required|exists:attendance_units,id',
            'priorities'                =>  'array',
            'priorities.*.description'  =>  'required|min:3',
            'priorities.*.weight'       =>  'required|integer',
            'attendants'                =>  'array',
            'attendants.*.user_id'      =>  'required|exists:users,id',
            'attendants.*.user_cpf'     =>  'required',
            'managers'                  =>  'required|array',
            'managers.*.user_id'        =>  'required|exists:users,id',
            'managers.*.user_cpf'       =>  'required',
            'matters.*.description'      =>  'required|min:3',
            'managers.*.users.*'        =>  'required|exists:users,id',
            'ticket_withdrawal'         =>  'required',
            'ticket_sequence'           =>  'required',
            'reset_tickets_counter'     =>  'nullable|integer|min:1',
            'max_daily_tickets'         =>  'nullable|integer|min:1'
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active'                 =>  'ativo',
            'description'               =>  'descrição',
            'point_name'                =>  'nome ponto de atendimento',
            'attendance_units_id'       =>  'unidade de atendimento',
            'priorities.*.description'  =>  'descrição',
            'priorities.*.weight'       =>  'peso',
            'attendants.*.user_cpf'     =>  'usuário',
            'managers'                  =>  'gestores',
            'managers.*.user_cpf'       =>  'usuário',
            'matters.*.description'      => 'assunto',
            'managers.*.users.*'        =>  'usuário',
            'ticket_withdrawal'         =>  'formato de retirada de senhas',
            'ticket_sequence'           =>  'sequência de senhas',
            'reset_tickets_counter'     =>  'reiniciar senhas no contador',
            'max_daily_tickets'         =>  'qtd. máx. tickets diários',
        ];
    }
}
