<?php

namespace App\Models\System;

use App\Models\CoreModel;

class ModuleMenu extends CoreModel
{

    protected $table = 'modules_menus';
    protected $fillable = [
        'id', 'modules_menus_id_parent', 'modules_id', 'routine_actions_id', 'attributes', 'list_order'
    ];
}
