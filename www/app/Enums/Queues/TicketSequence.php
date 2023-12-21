<?php

namespace App\Enums\Queues;

use BenSampo\Enum\Enum;

final class TicketSequence extends Enum
{
    const PRIORITY = [
        'label' =>  'POR ORDEM DE PRIORIDADE',
        'value' =>  'priority',
    ];
    const ISSUE = [
        'label' =>  'POR ASSUNTO',
        'value' =>  'matter',
    ];
}
