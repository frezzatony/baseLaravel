<?php

namespace App\Lib\Process\Elements\Task;

class Service
{

    public static function run($userGroups, $process, $element)
    {
        $element['run_next_step'] = true;
        return $element;
    }
}
