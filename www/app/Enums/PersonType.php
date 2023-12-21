<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PersonType extends Enum
{
    const NATURAL = [
        'label' =>  'PESSOA FÍSICA',
        'value' =>  'natural',
    ];
    const JURIDICAL = [
        'label' =>  'PESSOA JURÍDICA',
        'value' =>  'juridical',
    ];
}
