<?php

namespace App\Models\System;

use App\Models\CoreModel;

class Module extends CoreModel
{

    protected $table = 'modules';
    protected $fillable = [
        'id', 'name', 'slug', 'is_active'
    ];
}
