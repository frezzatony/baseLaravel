<?php

namespace App\Helpers\Crud\System\Notification;

class NotificationHelper
{
    public static function searchFilters()
    {
        return [
            'id'  =>  [
                'label'         =>  'Código',
                'type'          =>  'textbox',
                'columns'       =>  ['id'],
                'hide_on_view'  =>  true,
            ],
            'title'  =>  [
                'label'     =>  'Título',
                'type'      =>  'textbox',
                'columns'   =>  ['title'],
            ],
            'resume'  =>  [
                'label'     =>  'Mensagem',
                'type'      =>  'textbox',
                'columns'   =>  ['resume'],
            ],
            'notifiable_id'  =>  [
                'label'         =>  'Usuário',
                'type'          =>  'integer',
                'columns'       =>  ['notifiable_id'],
                'hide_on_view'  =>  true,
            ],
        ];
    }

    public static function defaultSearchFilters()
    {
        return [
            'title'  =>  [
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
                'label'         =>  'Id',
                'column_list'   =>  "id",
                'order'         =>  false,
                'hide_on_view'  =>  true,
            ],
            [
                'label'         =>  'Remetente',
                'column_list'   =>  "author",
                'column_order'  =>  "author",
                'order'         =>  true,
            ],
            [
                'label'         =>  'Título',
                'column_list'   =>  "title",
                'column_order'  =>  "title",
                'order'         =>  true,
            ],
            [
                'label'         =>  'Resumo',
                'column_list'   =>  "resume",
                'column_order'  =>  "resume",
                'order'         =>  true,
            ],
            [
                'label'         =>  'Data Envio',
                'column_list'   =>  "created_at",
                'column_order'  =>  "created_at",
                'order'         =>  true,
            ],
            [
                'label'         =>  'Data Leitura',
                'column_list'   =>  "read_at",
                'column_order'  =>  "read_at",
                'order'         =>  true,
            ],
        ];
    }
}
