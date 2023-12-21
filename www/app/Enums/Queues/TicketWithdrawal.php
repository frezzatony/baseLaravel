<?php

namespace App\Enums\Queues;

use BenSampo\Enum\Enum;

final class TicketWithdrawal extends Enum
{
    const ATTENDANT_DISPENSER = [
        'label' =>  'ENTREGA POR ATENDENTE',
        'value' =>  'attendant_dispenser',
    ];
    const DISPENSER = [
        'label' =>  'DISPENSER',
        'value' =>  'dispenser',
    ];
}
