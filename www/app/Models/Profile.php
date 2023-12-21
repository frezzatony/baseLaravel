<?php

namespace App\Models;

class Profile extends CoreModel
{
    protected $table = 'profiles';
    protected $fillable = ['id', 'name', 'is_active'];
}
