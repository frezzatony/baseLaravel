<?php

namespace App\Lib\Process\Elements\Gateway;

class Gateway
{
    public static function run($process, $userGroups, $element)
    {
        switch ($element['type']) {
            case 'exclusive':
                return Exclusive::run($process, $userGroups, $element);
                break;
            case 'inclusive':
                return Inclusive::run($process, $userGroups, $element);
                break;
        }
    }
}
