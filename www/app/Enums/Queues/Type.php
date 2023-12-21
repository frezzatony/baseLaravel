<?php

namespace App\Enums\Queues;

use BenSampo\Enum\Enum;

final class Type extends Enum
{
    const FIRST_COME_TOTEM = [
        'label' =>  'POR ORDEM DE CHEGADA - SENHA TOTEM',
        'value' =>  'first_come_totem',
    ];
    const FIRST_COME_MANUAL = [
        'label' =>  'POR ORDEM DE CHEGADA - SENHA MANUAL',
        'value' =>  'first_come_manual',
    ];
}
