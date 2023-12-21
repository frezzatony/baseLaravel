<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Weekday extends Enum
{
    const MONDAY = [
        'label'     =>  'SEGUNDA-FEIRA',
        'isodow'    =>  1
    ];
    const TUESDAY = [
        'label'     =>  'TERÇA-FEIRA',
        'isodow'    =>  2
    ];
    const WEDNESDAY = [
        'label'     =>  'QUARTA-FEIRA',
        'isodow'    =>  3
    ];
    const THURSDAY = [
        'label'     =>  'QUINTA-FEIRA',
        'isodow'    =>  4
    ];
    const FRIDAY = [
        'label'     =>  'SEXTA-FEIRA',
        'isodow'    =>  5
    ];
    const SATURDAY = [
        'label'     =>  'SÁBADO',
        'isodow'    =>  6
    ];
    const SUNDAY = [
        'label'     =>  'DOMINGO',
        'isodow'    =>  7
    ];
}
