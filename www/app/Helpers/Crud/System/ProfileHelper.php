<?php

namespace App\Helpers\Crud\System;

class ProfileHelper
{
    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  ['id'],
            ],
            'name'  =>  [
                'label'     =>  'Nome',
                'type'      =>  'textbox',
                'columns'   =>  ['name']
            ],
            'is_active' =>  [
                'label'     =>  'Ativo',
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
                'column_order'  =>  'id::INT',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Nome',
                'column_list'   =>  'name',
                'column_order'  =>  'name',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Ativo',
                'column_list'   =>  'is_active',
                'column_order'  =>  'is_active',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return $value ? 'SIM' : 'NÃO';
                }
            ],
        ];
    }
}
