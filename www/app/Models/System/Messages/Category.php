<?php

namespace App\Models\System\Messages;

use App\Models\CoreModel;

class Category extends CoreModel
{
    protected $table = 'notifications.categories';
    protected $fillable = [
        'id', 'description', 'is_active'
    ];
}
