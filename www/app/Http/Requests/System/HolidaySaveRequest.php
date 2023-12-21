<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class HolidaySaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description'   =>  'required|min:3',
            'date'          =>  'required|date',
            'type'          =>  'required|in:' . implode(',', \App\Enums\HolidayType::getKeys()),
            'annual'        =>  'in:t,',
            'optional'      =>  'in:t,',
            'time_start'    =>  'date_format:H:i:s',
            'time_end'      =>  'date_format:H:i:s',
        ];
    }

    public function attributes(): array
    {
        return [
            'description'   =>  'descrição',
            'date'          =>  'data',
            'type'          =>  'tipo',
            'annual'        =>  'anual',
            'optional'      =>  'facultativo',
            'time_start'    =>  'hora início',
            'time_end'      =>  'hora final',
        ];
    }
}
