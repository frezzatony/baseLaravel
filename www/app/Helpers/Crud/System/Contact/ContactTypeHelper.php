<?php

namespace App\Helpers\Crud\System\Contact;

use App\Helpers\StringHelper;

class ContactTypeHelper
{
    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  ['id'],
            ],
            'description'  =>  [
                'label'     =>  'Descrição',
                'type'      =>  'textbox',
                'columns'   =>  ['description']
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
            'description'  =>  [
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
                'label'         =>  'Descrição',
                'column_list'   =>  'description',
                'column_order'  =>  'description',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return StringHelper::upper($value);
                }
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
