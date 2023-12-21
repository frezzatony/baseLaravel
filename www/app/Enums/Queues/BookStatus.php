<?php

namespace App\Enums\Queues;

use BenSampo\Enum\Enum;

final class BookStatus extends Enum
{
    const WAITING_IN_LINE = [
        'label' =>  'AGUARDANDO NA FILA',
        'value' =>  'waiting_in_line',
    ];
    const CALLING = [
        'label' =>  'CHAMANDO',
        'value' =>  'calling',
    ];
    const CANCELED = [
        'label' =>  'CANCELADO',
        'value' =>  'canceled',
    ];
    const ASSISTING = [
        'label' =>  'EM ATENDIMENTO',
        'value' =>  'assisting',
    ];
    const COMPLETED = [
        'label' =>  'CONCLUÍDO',
        'value' =>  'completed',
    ];
}
