<?php

namespace App\Lib\Process\Elements\Event;

class Event
{
    public static function run($process, $userGroups, $element)
    {
        switch ($element['type']) {
            case 'start_conditional':
                return StartConditional::run($process, $userGroups, $element);
                break;
            case 'end':
                dd('fim do processo');
                return End::run($process, $userGroups, $element);
                break;
        }
    }
}
