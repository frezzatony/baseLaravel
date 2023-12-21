<?php

namespace App\Models\System;

use App\Models\CoreModel;

class Routine extends CoreModel
{

    protected $table = 'routines';
    protected $fillable = [
        'id', 'name', 'slug', 'modules_id', 'is_active'
    ];
}
