<?php

namespace App\Lib\Process\Elements\Event;

class StartConditional
{

    public static function run($userGroups, $process, $element)
    {
        $element['run_next_step'] = true;
        return $element;
    }
}
