<?php

namespace App\Enums\Queues;

use BenSampo\Enum\Enum;

final class Activity extends Enum
{
    const QUEUING_UP = [
        'label' =>  'ENTRADA NA FILA',
        'value' =>  'queuing_up',
    ];
    const CALL = [
        'label' =>  'CHAMADA',
        'value' =>  'call',
    ];
    const CANCELLATION = [
        'label' =>  'CANCELAMENTO',
        'value' =>  'cancellation',
    ];
    const BEGIN = [
        'label' =>  'INÍCIO DO ATENDIMENTO',
        'value' =>  'begin',
    ];
    const UPDATE = [
        'label' =>  'ATUALIZAÇÃO NOS DADOS',
        'value' =>  'update',
    ];
    const CONCLUSION = [
        'label' =>  'ENCERRAMENTO DO ATENDIMENTO',
        'value' =>  'conclusion',
    ];
    const RATE = [
        'label' =>  'CLASSIFICAÇÃO',
        'value' =>  'rate',
    ];
}
