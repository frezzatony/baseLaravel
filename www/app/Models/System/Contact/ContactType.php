<?php

namespace App\Models\System\Contact;

use App\Models\CoreModel;

class ContactType extends CoreModel
{
    protected $table = 'contacts_types';
    protected $fillable = [
        'id', 'is_active', 'description',
    ];
}
