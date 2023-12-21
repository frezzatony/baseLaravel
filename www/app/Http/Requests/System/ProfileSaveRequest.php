<?php

namespace App\Http\Requests\System;

use App\Models\Profile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Request;

class ProfileSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profile = (int)$this->id ? Profile::find($this->id)->first() : null;

        $validation = [
            'actions'   =>  'array'
        ];

        if (!empty($profile) && $profile->can_edit) {
            $validation = array_merge($validation, [
                'is_active' =>  'in:t,f',
                'name'      =>  'required|min:4',
            ]);
        }

        return $validation;
    }

    public function attributes(): array
    {
        return [
            'is_active' =>  'ativo',
            'name'      =>  'nome',
            'actions'   =>  'privilégios',
        ];
    }
}
