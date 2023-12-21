<?php

namespace App\Models\System;

use App\Models\CoreModel;

class Holiday extends CoreModel
{

    protected $table = 'holidays';
    protected $fillable = [
        'id', 'description', 'date', 'type', 'optional', 'annual', 'time_start', 'time_end'
    ];
}
