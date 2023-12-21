<?php

namespace App\Helpers\Crud\System\Queue;

class QueueCallOrderHelper
{
    public static function searchFilters()
    {
        return [
            'queues_id'  =>  [
                'label'     =>  'Fila de Atendimento',
                'type'      =>  'integer',
                'columns'   =>  ['queues_id'],
            ],
        ];
    }
}
