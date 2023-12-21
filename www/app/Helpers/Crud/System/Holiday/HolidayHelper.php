<?php

namespace App\Helpers\Crud\System\Holiday;

class HolidayHelper
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
            'date'  =>  [
                'label'     =>  'Data',
                'type'      =>  'date',
                'columns'   =>  ['date']
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
            ],
            [
                'label'         =>  'Anual',
                'column_list'   =>  'annual',
                'column_order'  =>  'annual',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return $value ? 'SIM' : 'NÃO';
                }
            ],
            [
                'label'         =>  'Tipo',
                'column_list'   =>  'type',
                'column_order'  =>  'type',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return \App\Enums\HolidayType::fromKey($value);
                },
            ],
            [
                'label'         =>  'Data',
                'column_list'   =>  'date',
                'column_order'  =>  'date',
                'order'         =>  true,
            ],
        ];
    }
}
