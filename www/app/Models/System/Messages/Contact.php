<?php

namespace App\Models\System\Messages;

use App\Models\CoreModel;

class Contact extends CoreModel
{
    protected $table = 'notifications.contacts';
    protected $fillable = [
        'id', 'name', 'telegram', 'email', 'is_active'
    ];
}
