<?php

namespace App\Lib\Process\Elements\Task;

class User
{
    public static function run($userGroups, $process, $element)
    {
        $element['run_next_step'] = false;
        return $element;
    }
}
