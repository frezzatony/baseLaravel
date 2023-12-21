<?php

namespace App\Helpers\Crud\System;

class RoutineHelper
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
            'slug'  =>  [
                'label'     =>  'Slug',
                'type'      =>  'textbox',
                'columns'   =>  ['slug']
            ],
            'module'  =>  [
                'label'     =>  'Módulo',
                'type'      =>  'select',
                'columns'   =>  ['module_id']
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
                'label'         =>  'Nome rotina',
                'column_list'   =>  'name',
                'column_order'  =>  'name',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Slug',
                'column_list'   =>  'slug',
                'column_order'  =>  'slug',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Módulo',
                'column_list'   =>  'module_name',
                'column_order'  =>  'module_name',
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
            [
                'label'         =>  'Ações',
                'column_list'   =>  'routine_actions',
            ]
        ];
    }
}
