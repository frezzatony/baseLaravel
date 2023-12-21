<?php

namespace App\Models\System\CustomerService;

use App\Models\CoreModel;

class CustomerService extends CoreModel
{
    protected $fillable = [
        'id', 'book_id', 'status', 'activity', 'users_id_responsibility', 'tags', 'attachments_catalog_id', 'form_data', 'transferred_at', 'completed_at',
    ];
}
