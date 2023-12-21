<?php

namespace App\Enums\Queues;

use App\Helpers\StringHelper;
use BenSampo\Enum\Enum;

final class WeightOrder extends Enum
{
    const DISABILITIES_REDUCED_MOBILITY = [
        'weight'        =>  5,
        'description'   =>  'PRIORITÁRIO - PESSOA COM DEFICIÊNCIA OU MOBILIDADE REDUZIDA'
    ];
    const ELDERLY = [
        'weight'        =>  5,
        'description'   =>  'PRIORITÁRIO - PESSOA IDOSA COM 60 ANOS OU MAIS'
    ];
    const PREGNANT_LACTATING = [
        'weight'        =>  5,
        'description'   =>  'PRIORITÁRIO - MULHER GESTANTE OU LACTANTE'
    ];
    const WITHOUT_WEIGHT = [
        'weight'        =>  1,
        'description'   =>  'NORMAL'
    ];
}
