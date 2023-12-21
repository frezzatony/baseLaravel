<?php

namespace App\Helpers\Crud\System\Queue;

class QueueMatterHelper
{
    public static function searchFilters()
    {
        return [
            'queues_id'  =>  [
                'label'     =>  'Fila de Atendimento',
                'type'      =>  'integer',
                'columns'   =>  ['queue_id'],
            ],
            'user_attendant_id'    =>  [
                'label'     =>  'Atendente',
                'type'      =>  'integer',
                'columns'   =>  ['user_attendant_id'],
            ],
        ];
    }
}
