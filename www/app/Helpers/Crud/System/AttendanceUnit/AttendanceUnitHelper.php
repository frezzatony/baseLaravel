<?php

namespace App\Helpers\Crud\System\AttendanceUnit;

use App\Models\System\AttendanceUnit\AttendanceUnit;

class AttendanceUnitHelper
{
    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  [AttendanceUnit::getTableName() . '.id'],
            ],
            'name'  =>  [
                'label'     =>  'Nome',
                'type'      =>  'textbox',
                'columns'   =>  [AttendanceUnit::getTableName() . '.name']
            ],
            'slug'  =>  [
                'label'     =>  'Slug',
                'type'      =>  'textbox',
                'columns'   =>  [AttendanceUnit::getTableName() . '.slug']
            ],
            'is_active' =>  [
                'label'     =>  'Ativa',
                'type'      =>  'bool',
                'columns'   =>  [AttendanceUnit::getTableName() . '.is_active']
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
                'column_order'  =>  AttendanceUnit::getTableName() . '.id::INT',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Nome',
                'column_list'   =>  'name',
                'column_order'  =>  AttendanceUnit::getTableName() . '.name',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Slug',
                'column_list'   =>  'slug',
                'column_order'  =>  AttendanceUnit::getTableName() . '.slug',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Ativa',
                'column_list'   =>  'is_active',
                'column_order'  =>  AttendanceUnit::getTableName() . '.is_active',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return $value ? 'SIM' : 'NÃO';
                }
            ],
        ];
    }
}
