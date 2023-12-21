<?php

namespace App\Helpers\Crud\System\Messages;

use App\Helpers\StringHelper;

class ContactHelper
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
            'telegram'  =>  [
                'label'     =>  'Telegram',
                'type'      =>  'textbox',
                'columns'   =>  ['telegram']
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
                'list_format'   =>  function ($value) {
                    return StringHelper::upper($value);
                }
            ],
            [
                'label'         =>  'Telegram',
                'column_list'   =>  'telegram',
                'column_order'  =>  'telegram',
                'order'         =>  true,
            ],
            [
                'label'         =>  'E-mail',
                'column_list'   =>  'email',
                'column_order'  =>  'email',
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
