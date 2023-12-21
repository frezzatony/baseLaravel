<?php

namespace App\Models;

use App\Models\CoreModel;

class AttachmentCatalog extends CoreModel
{
    protected $table = 'attachments_catalog';
    protected $fillable = [
        'catalog'
    ];
}
