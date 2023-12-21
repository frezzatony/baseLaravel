<?php

namespace App\Http\Requests\System\Queue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FirstComeTotemSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request): array
    {
        $this->setRules($request);
        return array_merge($this->rules['rules'], [
            'is_active'                 =>  'in:t,f',
            'description'               =>  'required|min:3',
            'point_name'                =>  'required|min:3',
            'attendance_units_id'       =>  'required|exists:attendance_units,id',
            'operation'                 =>  'required|array',
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
            'calendar.*.date'           =>  'required|date',
            'calendar.*.hours'          =>  'required_if:calendar.*.full_day,f',
            'max_daily_tickets'         =>  'nullable|integer|min:1'
        ]);
    }

    public function attributes(): array
    {
        return array_merge($this->rules['labels'], [
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
            'calendar.*.date'           =>  'data',
            'max_daily_tickets'         =>  'qtd. máx. tickets diários',
        ]);
    }

    public function messages()
    {
        return $this->rules['messages'] ?? [];
    }

    private function setRules(Request $request)
    {
        $this->rules = [];
        foreach (\App\Enums\Weekday::asArray() as $weekday) {
            $this->rules['rules']["operation.{$weekday['isodow']}.availability"] = 'required|in:t,f';
            $this->rules['rules']["operation_{$weekday['isodow']}_hours"] = "array|required_if:operation.{$weekday['isodow']}.availability,t";
            $this->rules['rules']["operation_{$weekday['isodow']}_hours.*.start"] = "required|date_format:H:i|before:operation_{$weekday['isodow']}_hours.*.end";
            $this->rules['rules']["operation_{$weekday['isodow']}_hours.*.end"] = 'required|date_format:H:i';

            $this->rules['labels']["operation.{$weekday['isodow']}.availability"] = 'atendimento';
            $this->rules['labels']["operation_{$weekday['isodow']}_hours"] = 'horários de atendimento';
            $this->rules['labels']["operation_{$weekday['isodow']}_hours.*.start"] = 'início';
            $this->rules['labels']["operation_{$weekday['isodow']}_hours.*.end"] = 'termino';

            $this->rules['messages']["operation_{$weekday['isodow']}_hours.required_if"] = 'O campo horários de atendimento é obrigatório';
            $this->rules['messages']["operation_{$weekday['isodow']}_hours.*.start.before"] = 'O campo início deve ser um horário anterior ao término.';
        }

        foreach ($request->get('calendar') ?? [] as $key => $calendar) {
            $this->rules['rules']["calendar.$key.full_day"] = 'in:t,f';
            $this->rules['rules']["calendar.$key.availability"] = 'in:t,f';

            $this->rules['labels']["calendar.$key.full_day"] = 'dia todo';
            $this->rules['labels']["calendar.$key.availability"] = 'haverá atendimento';

            if ($calendar['full_day'] == 'f' && empty($request->input("calendar.$key.hours"))) {
                $this->rules['rules']["calendar_hours[$key]"] = 'required|array';
                $this->rules['labels']["calendar_hours[$key]"] = 'horários';
            }
            if ($calendar['full_day'] == 'f' && !empty($request->input("calendar.$key.hours"))) {
                $this->rules['rules']["calendar.$key.hours.*.start"] = "required|before:calendar.$key.hours.*.end";
                $this->rules['rules']["calendar.$key.hours.*.end"] = 'required';

                $this->rules['labels']["calendar.$key.hours.*.start"] = 'início';
                $this->rules['labels']["calendar.$key.hours.*.end"] = 'término';

                $this->rules['messages']["calendar.$key.hours.*.start.before"] = 'O campo início deve ser um horário anterior ao término.';
            }
        }
    }
}
