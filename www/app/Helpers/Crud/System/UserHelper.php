<?php

namespace App\Helpers\Crud\System;

class UserHelper
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
                'label'     =>  'Nome/Nome Social',
                'type'      =>  'textbox',
                'columns'   =>  ['name', 'social_name']
            ],
            'login'  =>  [
                'label'     =>  'Login',
                'type'      =>  'textbox',
                'columns'   =>  ['login']
            ],
            'email'  =>  [
                'label'     =>  'E-mail',
                'type'      =>  'textbox',
                'columns'   =>  ['email']
            ],
            'name_login'    =>  [
                'label'     =>  'Login',
                'type'      =>  'textbox',
                'columns'   =>  ['name', 'login']
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
                'column_list'   =>  'name_show',
                'column_order'  =>  'name_show',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Login',
                'column_list'   =>  'login',
                'column_order'  =>  'login',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $value);
                }
            ],
        ];
    }
}
