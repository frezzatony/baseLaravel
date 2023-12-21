<?php

namespace App\Models\System\Messages;

use App\Models\CoreModel;

class ContactCategory extends CoreModel
{
    protected $table = 'notifications.contacts_categories';
    protected $fillable = [
        'id', 'notifications_contacts_id', 'notifications_categories_id',
    ];
}
