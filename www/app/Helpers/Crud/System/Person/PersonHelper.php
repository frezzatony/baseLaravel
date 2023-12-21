<?php

namespace App\Helpers\Crud\System\Person;

use App\Helpers\StringHelper;

class PersonHelper
{
    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  ['persons.id'],
            ],
            'name'  =>  [
                'label'     =>  'Nome/Razão Social',
                'type'      =>  'textbox',
                'columns'   =>  ['name', 'social_name']
            ],
            'cpf_cnpj'  =>  [
                'label'     =>  'CPF/CNPJ',
                'type'      =>  'textbox',
                'columns'   =>  ['cpf_cnpj']
            ],
            'is_active' =>  [
                'label'     =>  'Ativa',
                'type'      =>  'bool',
                'columns'   =>  ['is_active']
            ],
        ];
    }

    public static function defaultSearchFilters()
    {
        return [
            'name'  =>  [
                [
                    'value'     =>  '',
                    'operator'  =>  'contains'
                ]
            ]
        ];
    }

    public static function listItems()
    {
        return [
            [
                'label'         =>  'Código',
                'column_list'   =>  'id',
                'column_order'  =>  'persons.id::INT',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Nome/Razão Social',
                'column_list'   =>  'name_show',
                'column_order'  =>  'name_show',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return StringHelper::upper($value);
                }
            ],
            [
                'label'         =>  'Tipo de Pessoa',
                'column_list'   =>  'type_show',
                'column_order'  =>  'type_show',
                'order'         =>  true,
            ],
            [
                'label'         =>  'CPF/CNPJ',
                'column_list'   =>  'cpf_cnpj',
                'column_order'  =>  'cpf_cnpj',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return strlen($value) == 11 ? mask('###.###.###-##', $value) :  mask('##.###.###/####-##', $value);
                }
            ],
            [
                'label'         =>  'Ativa',
                'column_list'   =>  'is_active',
                'column_order'  =>  'is_active',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return $value ? 'SIM' : 'NÃO';
                }
            ],
            [
                'label'         =>  'Tipo de Pessoa',
                'column_list'   =>  'type',
                'column_order'  =>  'type',
            ],
        ];
    }
}
