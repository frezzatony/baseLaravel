<?php

namespace App\Lib\Process\Elements\Task;

class Task
{
    public static function run($process, $userGroups, $element)
    {
        switch ($element['type']) {
            case 'user':
                return User::run($process, $userGroups, $element);
                break;
            case 'service':
                return Service::run($process, $userGroups, $element);
                break;
        }
    }
}
