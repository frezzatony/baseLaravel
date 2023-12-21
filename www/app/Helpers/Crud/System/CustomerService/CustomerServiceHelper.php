<?php

namespace App\Helpers\Crud\System\CustomerService;

class CustomerServiceHelper
{

    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'     =>  'Código',
                'type'      =>  'integer',
                'columns'   =>  ['customer_service_id'],
            ],
            'queue_id'  =>  [
                'label'     =>  'Fila de Atendimento',
                'type'      =>  'integer',
                'columns'   =>  ['queue_id'],
            ],
            'user_id'  =>  [
                'label'     =>  'Usuário Responsável',
                'type'      =>  'integer',
                'columns'   =>  ['users_id_responsibility'],
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
        ];
    }
}
