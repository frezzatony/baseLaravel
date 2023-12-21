<?php

namespace App\Models\System\Person;

use App\Models\CoreModel;

class Person extends CoreModel
{

    protected $table = 'persons';
    protected $fillable = [
        'id', 'type', 'name', 'social_name', 'birthdate', 'cpf_cnpj', 'addresses_id', 'attachments_catalog_id'
    ];
}
