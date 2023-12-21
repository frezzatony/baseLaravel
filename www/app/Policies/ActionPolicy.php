<?php

namespace App\Policies;

use App\Models\User;

class ActionPolicy
{

    public function __call($name, $arguments)
    {
        dd($name);
    }

    public function index()
    {
        return false;
    }
}
