<?php

namespace App\Helpers\Crud\System\Queue;

use App\Helpers\StringHelper;

class QueueHelper
{

    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  ['id'],
            ],
            'description'   =>  [
                'label'     =>  'Descrição',
                'type'      =>  'textbox',
                'columns'   =>  ['description']
            ],
            'type'          =>  [
                'label'     =>  'Tipo',
                'type'      =>  'select',
                'columns'   =>  ['type'],
                'values'    =>  array_merge(
                    ['' => 'TODAS'],
                    array_reduce(\App\Enums\Queues\Type::asArray(),  function ($result, $item) {
                        $result[$item['value']] = $item['label'];
                        return $result;
                    }, [])
                ),
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
            'description'   =>  [
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
                'label'         =>  'Tipo',
                'column_list'   =>  'type',
                'column_order'  =>  'type',
                'order'         =>  true,
                'list_format'   =>  function ($value) {
                    return StringHelper::upper(\App\Enums\Queues\Type::fromKey(StringHelper::upper($value))->value['label']);
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
        ];
    }

    public static function listProperties()
    {
        return [
            'type'  => [
                'column'        =>  'type'
            ],
        ];
    }
}
