<?php

namespace App\Enums\CustomerServices;

use BenSampo\Enum\Enum;

final class CustomerServiceStatus extends Enum
{
    const ASSISTING = [
        'label' =>  'EM ATENDIMENTO',
        'value' =>  'assisting',
    ];
    const CANCELED = [
        'label' =>  'CANCELADO',
        'value' =>  'canceled',
    ];
    const TRANSFERRED = [
        'label' =>  'TRANSFERIDO',
        'value' =>  'transferred',
    ];
    const COMPLETED = [
        'label' =>  'ENCERRADO',
        'value' =>  'completed',
    ];
}
