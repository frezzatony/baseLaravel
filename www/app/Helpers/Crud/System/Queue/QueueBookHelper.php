<?php

namespace App\Helpers\Crud\System\Queue;

class QueueBookHelper
{

    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  ['id'],
            ],
            'date'  =>  [
                'label'     =>  'Data',
                'type'      =>  'date',
                'columns'   =>  ['created_at'],
            ],

        ];
    }

    public static function listItems()
    {
        return [
            [
                'label'         =>  'Ticket',
                'column_list'   =>  'ticket',
                'column_order'  =>  'ticket::INT',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Entrada na Fila',
                'column_list'   =>  'created_at',
                'column_order'  =>  'created_at',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Assunto',
                'column_list'   =>  'matter_description',
                'column_order'  =>  'matter_description',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Prioridade',
                'column_list'   =>  'priority_description',
                'column_order'  =>  'priority_description',
                'order'         =>  true,
            ],
            [
                'label'         =>  'Situação',
                'column_list'   =>  'status_description',
                'column_order'  =>  'status_description',
                'order'         =>  true,
            ],
        ];
    }
}
