<?php

namespace App\Helpers\Crud\System;

class RoutineActionHelper
{
    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  ['id'],
            ],
            'slug'  =>  [
                'label'     =>  'slug',
                'type'      =>  'textbox',
                'columns'   =>  ['slug']
            ],
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
                'label'         =>  'Slug',
                'column_list'   =>  'slug',
                'column_order'  =>  'slug',
                'order'         =>  true,
            ],
        ];
    }
}
