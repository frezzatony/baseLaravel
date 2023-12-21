<?php

namespace App\Helpers\Totems;


class Totem1Helper
{
    public function screens()
    {
        return [
            1 => [
                'default'   =>  true,
                'title'     =>  'Selecione o setor que deseja atendimento',
                'buttons'   => [
                    1 => [
                        'title'     =>  'URBANISMO',
                        'target'    =>  2
                    ],
                    2 => [
                        'title'     =>  'TRIBUTAÇÃO',
                        'target'    =>  'queue.urbanismo'
                    ],
                    3 => [
                        'title'     =>  'URBANISMO',
                        'target'    =>  'queue.urbanismo'
                    ],
                    4 => [
                        'title'     =>  'TRIBUTAÇÃO',
                        'target'    =>  'queue.urbanismo'
                    ],
                ]
            ],
            2   =>  [
                'queues_id' =>  30,
                'subtitle'  =>  'Selecione o assunto para atendimento',
            ]
        ];
    }
}
