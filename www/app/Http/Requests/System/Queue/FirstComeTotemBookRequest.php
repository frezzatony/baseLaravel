<?php

namespace App\Http\Requests\System\Queue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FirstComeTotemBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request): array
    {
        return  [
            'queue'         =>  'required|exists:queues,id',
            'matter'         =>  ['required', Rule::exists('queues_matters', 'id')->where(function ($query) {
                $query->where('queues_id', $this->input('queue'))
                    ->where('id', $this->input('matter'));
            }),],
            'call_order'    =>  ['required', Rule::exists('queues_call_orders', 'id')->where(function ($query) {
                $query->where('queues_id', $this->input('queue'))
                    ->where('id', $this->input('call_order'));
            }),],
        ];
    }
}
