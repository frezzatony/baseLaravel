<?php

namespace App\Models\System\AttendanceUnit;

use App\Models\CoreModel;

class AttendanceUnit extends CoreModel
{

    protected $table = 'attendance_units';
    protected $fillable = [
        'id', 'name', 'slug', 'addresses_id', 'page_file', 'attachments_catalog_id', 'web_page'
    ];
}
