<?php

namespace App\Helpers\Crud\System\CustomerService\Forms;

class DefaultHelper
{
    public static function inputs()
    {
        return [
            [
                'type'      =>  'group',
                'id'        =>  'person',
                'label'     =>  'Dados Pessoa Interessada',
                'children'    =>  [
                    [
                        'id'    =>  'person_id',
                        'type'  =>  'input',
                        'label' =>  'Código',
                    ],
                ]
            ],
            [
                'type'      =>  'group',
                'id'        =>  'comments',
                'label'     =>  'Observações',
                'children'    =>  [
                    [
                        'id'    =>  'comments_public',
                        'type'  =>  'input',
                        'label' =>  'Observações Públicas',
                    ],
                    [
                        'id'    =>  'comments_internal',
                        'type'  =>  'input',
                        'label' =>  'Observações Internas',
                    ],
                ]
            ]
        ];
    }
}
